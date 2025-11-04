<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\BreakTime;
use App\Models\Attendance;
use App\Models\BreakRequest;
use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceRequestFormRequest;

class AttendanceController extends Controller
{
    //今日の勤怠
    public function index(Request $request)
    {
        $user = Auth::user();
        $day = $request->input('day') ?? Carbon::today();

        $date = Carbon::parse($day);

        $attendances = Attendance::with('user')
            ->whereDate('work_date', $date)
            ->get();

        return view('admin/attendance_today', compact('attendances', 'day'));
    }


    public function showStaffList()
    {
        $users = User::with('attendances')
            ->select('id', 'name', 'email')
            ->where('role', 0)
            ->get();

        return view('admin/staff_list', compact('users'));
    }

    // スタッフ別月次勤怠
    public function showStaffAttendance(Request $request, $id)
    {
        $attendances = Attendance::with(['user', 'breakTimes'])
            ->findOrFail($id);

        $month = $request->input('month', Carbon::now()->format('Y-m'));



        return view('admin/staff_attendance_list', compact('attendances'));
    }




    public function showRequest()
    {
        return view('admin/request_list');
    }




    public function showDetail(Request $request, $id)
    {
        $attendance = Attendance::with('user', 'breakTimes', 'attendanceRequests.breakRequests')
            ->findOrFail($id);

        $pendingRequest = $attendance->attendanceRequests()
            ->where('status', AttendanceRequest::STATUS_PENDING)
            ->first();

        return view('admin.attendance_detail', compact('attendance', 'pendingRequest'));
    }

    //修正申請を送信
    public function updateAttendance(AttendanceRequestFormRequest $request, $id)
    {
        if (auth()->user()->role != User::ROLE_ADMIN) {
            return redirect()->route('admin.login')->with('error', '管理者ではない為、再度ログインしてください。');
        }

        $validated = $request->validated();
        $attendance = Attendance::findOrFail($id);

        $existingRequest = $attendance->attendanceRequests()->where('status', AttendanceRequest::STATUS_PENDING)->exists();


        if ($existingRequest) {
            return redirect()->back()->with('error', 'すでに承認待ちの申請があります。');
        }

        DB::beginTransaction();
        try {
            //出勤・退勤が入力されてる場合のみ更新
            if ($validated['start_time'] && $validated['end_time']) {
                $attendance->update([
                    'start_time' => Carbon::parse($attendance->work_date->format('Y-m-d') . ' ' . $validated['start_time']),
                    'end_time' => Carbon::parse($attendance->work_date->format('Y-m-d') . ' ' . $validated['end_time']),
                    'admin_note' => $validated['reason'],
                ]);
            }
            // 既存の休憩時間を更新または削除
            $existingBreaks = $attendance->breakTimes;
            // 送信された休憩データを処理
            if (!empty($validated['break_start'])) {
                foreach ($validated['break_start'] as $index => $breakStart) {
                    $breakEnd = $validated['break_end'][$index] ?? null;

                    // 開始と終了が両方入力されている場合のみ処理
                    if ($breakStart && $breakEnd) {
                        $breakStartTime = Carbon::parse($attendance->work_date->format('Y-m-d') . ' ' . $breakStart);
                        $breakEndTime = Carbon::parse($attendance->work_date->format('Y-m-d') . ' ' . $breakEnd);

                        // 既存の休憩がある場合は更新
                        if (isset($existingBreaks[$index])) {
                            $existingBreaks[$index]->update([
                                'break_start' => $breakStartTime,
                                'break_end' => $breakEndTime,
                            ]);
                        } else {
                            // 新規の休憩を追加
                            BreakTime::create([
                                'attendance_id' => $attendance->id,
                                'break_start' => $breakStartTime,
                                'break_end' => $breakEndTime,
                            ]);
                        }
                    }
                }
            }
            // 送信されなかった既存の休憩を削除
            // 例）休憩が３つあったのに、２つしか送信されなかった場合、３つ目の余りを削除


            $submittedBreaksCount = !empty($validated['break_start'])
                ? count(array_filter($validated['break_start']))
                : 0;
            if ($existingBreaks->count() > $submittedBreaksCount) {
                for ($i = $submittedBreaksCount; $i < $existingBreaks->count(); $i++) {
                    if (isset($existingBreaks[$i])) {
                        $existingBreaks[$i]->delete();
                    }
                }
            }
            DB::commit();

            return redirect()->route('admin.attendance.detail', $id)
                ->with('success', '勤怠情報を更新しました。');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', '更新に失敗しました。' . $e->getMessage())->withInput();
        }
    }
}
