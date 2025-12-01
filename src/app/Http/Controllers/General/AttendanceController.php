<?php

namespace App\Http\Controllers\General;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\BreakTime;
use App\Models\Attendance;
use App\Models\BreakRequest;
use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequestFormRequest;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        //今日の勤怠を取得
        $attendance = Attendance::where('user_id', $user->id)->whereDate('work_date', $today)->first();

        return view('general.attendance', compact('attendance'));
    }

    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)->whereDate('work_date', $today)->first();

        $now = Carbon::now();

        switch ($request->action) {
            case 'start': //出勤
                Attendance::create([
                    'user_id' => $user->id,
                    'work_date' => $today,
                    'start_time' => $now,
                    'status' => Attendance::STATUS_WORKING,
                ]);

                break;

            case 'end': //退勤
                if ($attendance) {
                    $attendance->update([
                        'end_time' => $now,
                        'status' => Attendance::STATUS_FINISHED,
                    ]);
                }
                break;

            case 'break_start': //休憩入
                if ($attendance) {
                    $attendance->update([
                        'status' => Attendance::STATUS_BREAK,
                    ]);
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $now,
                        'break_end' => null,
                    ]);
                }
                break;
            case 'break_end': //休憩戻
                if ($attendance) {
                    $attendance->update(['status' => Attendance::STATUS_WORKING]);

                    $attendance->breakTimes()->latest()->first()->update([
                        'break_end' => $now,
                    ]);
                }
                break;
        }
        return redirect()->route('general.attendance');
    }

    public function showList(Request $request)
    {

        $user = Auth::user();

        // 表示する月(なければ今月)
        $month = $request->input('month', Carbon::now()->format('Y-m'));

        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $attendanceData = Attendance::with('breakTimes')
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->orderBy('work_date')
            ->get()
            ->keyBy(function ($item) {
                return $item->work_date->format('Y-m-d');  // ← これで日付だけをキーにする
            });


        //その月の全日付を生成
        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        //全日付に対して、勤怠データがあればそれを、なければnullを返す
        $attendances = collect($period)->map(function ($date) use ($attendanceData) {
            $dateString = $date->format('Y-m-d');

            return $attendanceData->get($dateString)
                ?? $this->createEmptyAttendance($dateString);
        });

        return view('general.attendance_list', compact('attendances', 'month'));
    }

    private function createEmptyAttendance($dateString)
    {
        return (object)[
            'work_date' => $dateString,
            'start_time' => null,
            'end_time' => null,
            'breakTimes' => collect(),  // 空のコレクション
            'break_duration' => 0,
            'work_duration' => 0,
        ];
    }

    public function showDetail($id)
    {
        $attendance = Attendance::with([
            'user',
            'breakTimes',
            'attendanceRequests.breakRequests'
        ])->findOrFail($id);

        if ($attendance->user_id !== auth()->id()) {
            Auth::logout(); //ログアウト(セッション削除)
            return redirect()->route('login')->with('error', 'アクセス権限がありません。再度ログインしてください。');
        }

        $pendingRequest = $attendance->attendanceRequests
            ->where('status', AttendanceRequest::STATUS_PENDING)
            ->sortByDesc('created_at')
            ->first();


        return view('general.attendance_detail', compact('attendance', 'pendingRequest'));
    }


    //修正申請を送信
    public function submitRequest(AttendanceRequestFormRequest $request, $id)
    {
        $validated = $request->validated();
        $attendance = Attendance::findOrFail($id);

        //本人確認
        if ($attendance->user_id !== auth()->id()) {
            return redirect()->back()->with('error', 'アクセス権限がありません。');
        }

        //すでに承認待ちの申請があるか確認
        $existingRequest = $attendance->attendanceRequests()
            ->where('status', AttendanceRequest::STATUS_PENDING)
            ->exists();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'すでに承認待ちの申請があります。');
        }


        DB::beginTransaction();
        try {
            //出勤・勤怠が入力されているか確認
            $hasStartEnd = $validated['start_time'] && $validated['end_time'];

            //勤怠修正申請を作成
            $attendanceRequest = AttendanceRequest::create([
                'user_id' => auth()->id(),
                'attendance_id' => $attendance->id,
                'request_date' => now(),
                'requested_start_time' => $hasStartEnd
                    ? Carbon::parse($attendance->work_date->format('Y-m-d') . ' ' . $validated['start_time'])
                    : null,
                'requested_end_time' => $hasStartEnd
                    ? Carbon::parse($attendance->work_date->format('Y-m-d') . ' ' . $validated['end_time'])
                    : null,
                'reason' => $validated['reason'],
                'status' => AttendanceRequest::STATUS_PENDING,
            ]);

            //休憩時間の修正申請を作成
            if (!empty($validated['break_start'])) {
                foreach ($validated['break_start'] as $index => $breakStart) {
                    $breakEnd = $validated['break_end'][$index] ?? null;

                    if ($breakStart && $breakEnd) {
                        //既存の休憩か新規かを鑑定
                        $existingBreak = $attendance->breakTimes->get($index);

                        BreakRequest::create([
                            'attendance_request_id' => $attendanceRequest->id,
                            'break_time_id' => $existingBreak ? $existingBreak->id : null,
                            'requested_break_start' => Carbon::parse($attendance->work_date->format('Y-m-d') . ' ' . $breakStart),
                            'requested_break_end' => Carbon::parse($attendance->work_date->format('Y-m-d') . ' ' . $breakEnd),
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('attendance.detail', $id)
                ->with('success', '修正申請を送信しました。');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', '修正申請の送信に失敗しました。')
                ->withInput();
        }
    }
}
