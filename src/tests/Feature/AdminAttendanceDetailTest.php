<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAttendanceDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    //勤怠詳細画面に表示されているデータが選択したものになっている
    public function test_admin_attendance_detail_date()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $users = User::factory()->count(3)->create();

        $attendances = [];
        foreach ($users as $user) {
            $attendances[] = Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => now()->addDay()->toDateString(),
                'start_time' => now()->addDay()->setTime(9, 0),
                'end_time' => now()->addDay()->setTime(18, 0),
                'total_work_time' => 480,
                'status' => Attendance::STATUS_FINISHED,
            ]);
        }

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));
        $response->assertStatus(200);

        $firstAttendance = $attendances[0];

        $response = $this->get(route('admin.attendance.detail', ['id' => $firstAttendance->id]));

        $response->assertSee($firstAttendance->name);
        $response->assertSee($firstAttendance->work_date->format('Y年'));
        $response->assertSee($firstAttendance->work_date->format('n月j日'));
    }

    //出勤時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_admin_attendance_detail_validate_start_time()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $users = User::factory()->count(3)->create();

        $attendances = [];
        foreach ($users as $user) {
            $attendances[] = Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => now()->addDay()->toDateString(),
                'start_time' => now()->addDay()->setTime(9, 0),
                'end_time' => now()->addDay()->setTime(18, 0),
                'total_work_time' => 480,
                'status' => Attendance::STATUS_FINISHED,
            ]);
        }

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));
        $response->assertStatus(200);

        $firstAttendance = $attendances[0];

        $response = $this->get(route('admin.attendance.detail', ['id' => $firstAttendance->id]));


        $startTime = \Carbon\Carbon::parse('2025-12-01 18:00:00')->format('H:i');
        $endTime = \Carbon\Carbon::parse('2025-12-01 09:00:00')->format('H:i');

        $response = $this->post(
            route('admin.attendance.update', ['id' => $firstAttendance->id]),
            [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'reason' => '理由',
            ]
        );

        $response->assertSessionHasErrors([
            'end_time' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    //休憩開始時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_attendance_detail_validate_break_start_time()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $users = User::factory()->count(3)->create();

        $attendances = [];
        foreach ($users as $user) {
            $attendances[] = Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => now()->addDay()->toDateString(),
                'start_time' => now()->addDay()->setTime(9, 0),
                'end_time' => now()->addDay()->setTime(18, 0),
                'total_work_time' => 480,
                'status' => Attendance::STATUS_FINISHED,
            ]);
        }

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));
        $response->assertStatus(200);

        $firstAttendance = $attendances[0];

        $response = $this->get(route('admin.attendance.detail', ['id' => $firstAttendance->id]));


        $startTime = \Carbon\Carbon::parse('2025-12-01 09:00:00')->format('H:i');
        $endTime = \Carbon\Carbon::parse('2025-12-01 18:00:00')->format('H:i');
        $breakStart = \Carbon\Carbon::parse('2025-12-01 08:00:00')->format('H:i');
        $breakEnd = \Carbon\Carbon::parse('2025-12-01 13:00:00')->format('H:i');

        $response = $this->post(
            route('admin.attendance.update', ['id' => $firstAttendance->id]),
            [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'break_start' => [$breakStart],
                'break_end' => [$breakEnd],
                'reason' => '理由',
            ]
        );
        $response->assertSessionHasErrors(['break_start.0' => '休憩時間は不適切な値です',]);
    }

    //休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_attendance_detail_validate_break_end_time()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $users = User::factory()->count(3)->create();

        $attendances = [];
        foreach ($users as $user) {
            $attendances[] = Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => now()->addDay()->toDateString(),
                'start_time' => now()->addDay()->setTime(9, 0),
                'end_time' => now()->addDay()->setTime(18, 0),
                'total_work_time' => 480,
                'status' => Attendance::STATUS_FINISHED,
            ]);
        }

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));
        $response->assertStatus(200);

        $firstAttendance = $attendances[0];

        $response = $this->get(route('admin.attendance.detail', ['id' => $firstAttendance->id]));


        $startTime = \Carbon\Carbon::parse('2025-12-01 09:00:00')->format('H:i');
        $endTime = \Carbon\Carbon::parse('2025-12-01 18:00:00')->format('H:i');
        $breakStart = \Carbon\Carbon::parse('2025-12-01 09:00:00')->format('H:i');
        $breakEnd = \Carbon\Carbon::parse('2025-12-01 19:00:00')->format('H:i');

        $response = $this->post(
            route('admin.attendance.update', ['id' => $firstAttendance->id]),
            [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'break_start' => [$breakStart],
                'break_end' => [$breakEnd],
                'reason' => '理由',
            ]
        );
        $response->assertSessionHasErrors(['break_end.0' => '休憩時間もしくは退勤時間が不適切な値です',]);
    }

    //備考欄が未入力の場合エラーメッセージが表示される
    public function test_admin_attendance_detail_validate_reason()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->addDay()->toDateString(),
            'start_time' => now()->addDay()->setTime(9, 0),
            'end_time' => now()->addDay()->setTime(18, 0),
            'total_work_time' => 480,
            'status' => Attendance::STATUS_FINISHED,
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));
        $response->assertStatus(200);

        $response = $this->get(route('admin.attendance.detail', ['id' => $attendance->id]));

        $response = $this->post(
            route('admin.attendance.update', ['id' => $attendance->id]),
            [
                'reason' => '',
            ]
        );

        $response->assertSessionHasErrors(['reason' => '備考を記入してください']);
    }
}
