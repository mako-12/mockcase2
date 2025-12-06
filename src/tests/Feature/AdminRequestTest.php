<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminRequestTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_admin_attendance_detail_show_pending_all()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $user = User::factory()->create();

        $attendanceToday = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now()->setTime(9, 0),
            'end_time' => now()->setTime(18, 0),
            'total_work_time' => 480,
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $attendanceSubDay = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->subDay()->toDateString(),
            'start_time' => now()->subDay()->setTime(9, 0),
            'end_time' => now()->subDay()->setTime(18, 0),
            'total_work_time' => 480,
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $attendanceRequestToday = AttendanceRequest::create([
            'user_id' => $attendanceToday->user_id,
            'attendance_id' => $attendanceToday->id,
            // 'request_date' => now()->toDateString(),
            'request_date' => $attendanceToday->work_date,
            'request_start_time' => now()->setTime(10, 0),
            'request_end_time' => now()->setTime(19, 0),
            'reason' => '理由',
            'status' => AttendanceRequest::STATUS_PENDING
        ]);
        // dd($attendanceRequestToday);

        $attendanceRequestSubDay = AttendanceRequest::create([
            'user_id' => $attendanceSubDay->user_id,
            'attendance_id' => $attendanceSubDay->id,
            // 'request_date' => now()->subDay()->toDateString(),
            'request_date' => $attendanceSubDay->work_date,
            'request_start_time' => now()->subDay()->setTime(10, 0),
            'request_end_time' => now()->subDay()->setTime(19, 0),
            'reason' => '理由',
            'status' => AttendanceRequest::STATUS_PENDING
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.request'));

        $this->assertDatabaseHas('attendance_requests', [
            'id' => $attendanceToday->id,
            'status' => AttendanceRequest::STATUS_PENDING,
        ]);

        $this->assertDatabaseHas('attendance_requests', [
            'id' => $attendanceRequestSubDay->id,
            'status' => AttendanceRequest::STATUS_PENDING,
        ]);
    }

    //承認済みの修正申請が全て表示されている
    public function test_admin_attendance_detail_show_approved_all()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $user = User::factory()->create();

        $attendanceToday = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now()->setTime(9, 0),
            'end_time' => now()->setTime(18, 0),
            'total_work_time' => 480,
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $attendanceSubDay = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->subDay()->toDateString(),
            'start_time' => now()->subDay()->setTime(9, 0),
            'end_time' => now()->subDay()->setTime(18, 0),
            'total_work_time' => 480,
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $attendanceRequestToday = AttendanceRequest::create([
            'user_id' => $attendanceToday->user_id,
            'attendance_id' => $attendanceToday->id,
            // 'request_date' => now()->toDateString(),
            'request_date' => $attendanceToday->work_date,
            'request_start_time' => now()->setTime(10, 0),
            'request_end_time' => now()->setTime(19, 0),
            'reason' => '理由',
            'status' => AttendanceRequest::STATUS_PENDING
        ]);

        $attendanceRequestSubDay = AttendanceRequest::create([
            'user_id' => $attendanceSubDay->user_id,
            'attendance_id' => $attendanceSubDay->id,
            // 'request_date' => now()->subDay()->toDateString(),
            'request_date' => $attendanceSubDay->work_date,
            'request_start_time' => now()->subDay()->setTime(10, 0),
            'request_end_time' => now()->subDay()->setTime(19, 0),
            'reason' => '理由',
            'status' => AttendanceRequest::STATUS_APPROVED
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.request'));

        $this->assertDatabaseMissing('attendance_requests', [
            'id' => $attendanceToday->id,
            'status' => AttendanceRequest::STATUS_APPROVED,
        ]);

        $this->assertDatabaseHas('attendance_requests', [
            'id' => $attendanceRequestSubDay->id,
            'status' => AttendanceRequest::STATUS_APPROVED,
        ]);
    }

    //修正申請の詳細内容が正しく表示されている
    public function test_admin_attendance_detail_request_view_detail()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $user = User::factory()->create();

        $attendanceToday = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now()->setTime(9, 0),
            'end_time' => now()->setTime(18, 0),
            'total_work_time' => 480,
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $attendanceRequestToday = AttendanceRequest::create([
            'user_id' => $attendanceToday->user_id,
            'attendance_id' => $attendanceToday->id,
            // 'request_date' => now()->toDateString(),
            'request_date' => $attendanceToday->work_date,
            'request_start_time' => now()->setTime(10, 0),
            'request_end_time' => now()->setTime(19, 0),
            'reason' => '理由',
            'status' => AttendanceRequest::STATUS_PENDING
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.request'));

        $response = $this->actingAs($admin, 'admin')->get(route('admin.request.approval', ['attendance_correct_request_id' => $attendanceRequestToday->id]));

        $response->assertStatus(200);
        $response->assertSee('勤怠詳細');
        $response->assertSee($user->name);
    }

    //修正申請の承認処理が正しく行われる
    public function test_admin_attendance_detail_request_approval_push()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $user = User::factory()->create();

        $attendanceToday = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'start_time' => now()->setTime(9, 0),
            'end_time' => now()->setTime(18, 0),
            'total_work_time' => 480,
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $attendanceRequestToday = AttendanceRequest::create([
            'user_id' => $attendanceToday->user_id,
            'attendance_id' => $attendanceToday->id,
            'request_date' => $attendanceToday->work_date,
            'request_start_time' => now()->setTime(10, 0),
            'request_end_time' => now()->setTime(19, 0),
            'reason' => '理由',
            'status' => AttendanceRequest::STATUS_PENDING
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.request'));

        $response = $this->actingAs($admin, 'admin')->get(route('admin.request.approval', ['attendance_correct_request_id' => $attendanceRequestToday->id]));

        $response->assertSee('承認');

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.approve.request', $attendanceRequestToday->id))
            ->assertRedirect();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.request.approval', ['attendance_correct_request_id' => $attendanceRequestToday->id]));

        $response->assertSee('承認済み');

        $this->assertDatabaseHas('attendance_requests', [
            'id' => $attendanceRequestToday->id,
            'status' => AttendanceRequest::STATUS_APPROVED,
        ]);
    }
}
