<?php

namespace App\Http\Controllers\Admin;

use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AttendanceRequestController extends Controller
{
    public function showRequest()
    {

        $attendanceRequests = AttendanceRequest::with('user', 'attendance')
            ->get();

        return view('admin/request_list', compact('attendanceRequests'));
    }


    public function showRequestApproval($attendance_correct_request_id)
    {
        $attendanceRequest = AttendanceRequest::with('user', 'breakRequests', 'attendance')
            ->findOrFail($attendance_correct_request_id);


        return view('admin.request_approval', compact('attendanceRequest'));
    }

    public function approveRequest($attendance_correct_request_id)
    {
        $attendanceRequest = AttendanceRequest::with('user', 'attendance.breakTimes', 'breakRequests')
            ->findOrFail($attendance_correct_request_id);


        if ($attendanceRequest->status !== AttendanceRequest::STATUS_PENDING) {
            return redirect()->back()->with('error', 'この申請はすでに処理されています。');
        }

        DB::beginTransaction();
        try {
            // 出勤退勤時刻を更新(入力されている場合のみ)
            if ($attendanceRequest->requested_start_time && $attendanceRequest->requested_end_time) {
                $attendanceRequest->attendance->update([
                    'start_time' => $attendanceRequest->requested_start_time,
                    'end_time' => $attendanceRequest->requested_end_time,
                    'admin_note' => $attendanceRequest->reason,
                ]);
            }


            // 休憩時間を更新
            foreach ($attendanceRequest->breakRequests as $breakRequest) {
                if ($breakRequest->break_time_id) {
                    // 既存の休憩を更新
                    BreakTime::where('id', $breakRequest->break_time_id)->update([
                        'break_start' => $breakRequest->requested_break_start,
                        'break_end' => $breakRequest->requested_break_end,
                    ]);
                } else {
                    // 新規の休憩を作成
                    BreakTime::create([
                        'attendance_id' => $attendanceRequest->attendance_id,
                        'break_start' => $breakRequest->requested_break_start,
                        'break_end' => $breakRequest->requested_break_end,
                    ]);
                }
            }
            $attendanceRequest->update(['status' => AttendanceRequest::STATUS_APPROVED]);

            DB::commit();
            return redirect()->route('admin.request.approval', ['attendance_correct_request_id' => $attendance_correct_request_id])->with('success', '申請を承認しました。');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', '申請を承認できませんでした。')
                ->withInput();
        }
    }
}
