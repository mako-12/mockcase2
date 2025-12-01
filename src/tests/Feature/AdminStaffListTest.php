<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminStaffListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    //管理者ユーザーが全一般ユーザーの「氏名」「メールアドレス」を確認できる
    public function test_admin_staff_list_show_name_and_mail_address()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));

        $response = $this->actingAs($admin, 'admin')->get(route('admin.staff.list'));

        foreach ($users as $user) {
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    //ユーザーの勤怠情報が正しく表示される
    public function test_admin_staff_list_show_detail()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));

        $response = $this->actingAs($admin, 'admin')->get(route('admin.staff.list'));

        $firstUserId = $users->first();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.staff.attendance', ['id' => $firstUserId]));

        $response->assertSee($firstUserId->name . "さんの勤怠");
    }

    //「前月」を押下した時に表示月の前月の情報が表示される
    public function test_admin_staff_list_previous_month()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));

        $response = $this->actingAs($admin, 'admin')->get(route('admin.staff.list'));

        $firstUserId = $users->first();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.staff.attendance', ['id' => $firstUserId]));

        $month = Carbon::now()->format('Y-m');

        $previousMonth = Carbon::parse($month)->subMonth()->format('Y/m');

        $response = $this->get(route('admin.staff.attendance', [
            'id' => $firstUserId->id,
            'month' => $previousMonth
        ]));
        $response->assertSee($previousMonth);
    }

    //「翌月」を押下した時に表示月の翌月の情報が表示される
    public function test_admin_staff_list_next_month()
    {
        $admin = User::factory()->create([
            'role' => 1,
        ]);

        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.list'));

        $response = $this->actingAs($admin, 'admin')->get(route('admin.staff.list'));

        $firstUserId = $users->first();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.staff.attendance', ['id' => $firstUserId]));

        $month = Carbon::now()->format('Y-m');

        $addMonth = Carbon::parse($month)->addMonth()->format('Y/m');

        $response = $this->get(route('admin.staff.attendance', [
            'id' => $firstUserId->id,
            'month' => $addMonth
        ]));
        $response->assertSee($addMonth);
    }

    //詳細を押下すると、その日の勤怠詳細画面が表示される
    public function test_admin_staff_list_show_today_detail()
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

        $response = $this->actingAs($admin, 'admin')->get(route('admin.staff.list'));

        $response = $this->actingAs($admin, 'admin')->get(route('admin.staff.attendance', ['id' => $user->id]));

        $response = $this->actingAs($admin, 'admin')->get(route('admin.attendance.detail', ['id' => $attendance->id]));

        $response->assertSee('勤怠詳細');
        $response->assertSee($user->name);
    }
}
