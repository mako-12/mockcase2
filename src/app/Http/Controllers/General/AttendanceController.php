<?php

namespace App\Http\Controllers\General;

use Carbon\Carbon;
use App\Models\BreakTime;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

                    $attendance->breaks()->latest()->first()->update([
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

        $attendances = Attendance::with('breakTimes')->where('user_id', $user->id)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->orderBy('work_date')->get();

        return view('general.attendance_list', compact('attendances', 'month'));
    }
}
