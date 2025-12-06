<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAttendanceListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    //その日なされた全ユーザーの勤怠情報が正確に確認できる
    public function test_admin_attendance_list_all_user_of_the_day()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $users = User::factory()->count(3)->create();

        $attendance = [];
        foreach ($users as $user) {
            $attendance[] = Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => today()->toDateString(),
                'start_time' => today()->setTime(9, 0),
                'end_time' => today()->setTime(18, 0),
                'total_work_time' => 480,
                'status' => Attendance::STATUS_FINISHED,
            ]);
        }

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));

        $response->assertStatus(200);

        foreach ($users as $user) {
            $response->assertSee($user->name);
        }
    }

    //遷移した際に現在の日付が表示される
    public function test_admin_attendance_list_show_today_date()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $users = User::factory()->count(3)->create();

        $attendance = [];
        foreach ($users as $user) {
            $attendance[] = Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => today()->toDateString(),
                'start_time' => today()->setTime(9, 0),
                'end_time' => today()->setTime(18, 0),
                'total_work_time' => 480,
                'status' => Attendance::STATUS_FINISHED,
            ]);
        }

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));

        $response->assertStatus(200);

        $day = \Carbon\Carbon::today()->format('Y年m月d日');
        $response->assertSee($day);
    }

    //「前日」を押下した時に次の日の勤怠情報が表示される
    public function test_admin_attendance_list_show_the_day_before()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $users = User::factory()->count(3)->create();

        $attendances = [];
        foreach ($users as $user) {
            $attendances[] = Attendance::factory()->create([
                'user_id' => $user->id,
                'work_date' => now()->subDay()->toDateString(),
                'start_time' => now()->subDay()->setTime(9, 0),
                'end_time' => now()->subDay()->setTime(18, 0),
                'total_work_time' => 480,
                'status' => Attendance::STATUS_FINISHED,
            ]);
        }

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));

        $response->assertStatus(200);

        $day = \Carbon\Carbon::today();
        $response = $this->get(route('admin.attendance.list', ['day' => \Carbon\Carbon::parse($day)->subDay()->format('Y-m-d')]));

        foreach ($users as $user) {
            $response->assertSee($user->name);
        }
    }

    //「翌日」を押下した時に次の日の勤怠情報が表示される
    public function test_admin_attendance_list_show_the_next_day()
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

        $day = \Carbon\Carbon::today();
        $response = $this->get(route('admin.attendance.list', ['day' => \Carbon\Carbon::parse($day)->addDay()->format('Y-m-d')]));

        foreach ($users as $user) {
            $response->assertSee($user->name);
        }
    }
}
