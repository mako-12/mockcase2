<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceListTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    protected function setUp(): void
    {
        \Carbon\Carbon::setLocale('ja');
        parent::setUp();
    }

    //自分が行った勤怠情報がすべて表示される
    public function test_user_attendance_list_display()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        Carbon::setTestNow('2025-11-1 09:00:00');
        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);
        Carbon::setTestNow('2025-11-1 12:00:00');
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_start',
        ]);
        Carbon::setTestNow('2025-11-1 13:00:00');
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_end',
        ]);
        Carbon::setTestNow('2025-11-1 18:00:00');
        $response = $this->post(route('attendance.update'), [
            'action' => 'end',
        ]);

        $response = $this->get('/attendance/list');
        $response->assertSee([
            '11/01',
            '09:00',
            '18:00',
            '1:00',
            '8:00',
        ]);
    }

    //勤怠一覧画面に遷移した際に現在の月が表示される
    public function test_user_attendance_list_current_month()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $month = Carbon::now()->format('Y/m');


        $response = $this->get('/attendance/list');

        $response->assertSee($month);
    }
    //前月を押下した時に表示月の前月の情報が表示される
    public function test_user_attendance_list_previous_month()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $month = Carbon::now()->format('Y-m');

        $previousMonth = Carbon::parse($month)->subMonth()->format('Y/m');

        $response = $this->get('/attendance/list');

        $response = $this->get(route('attendance.list', [
            'month' => $previousMonth
        ]));
        $response->assertSee($previousMonth);
    }

    //翌月を押下した時に表示月の前月の情報が表示される
    public function test_user_attendance_list_next_month()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $month = Carbon::now()->format('Y-m');

        $addMonth = Carbon::parse($month)->addMonth()->format('Y/m');

        $response = $this->get('/attendance/list');

        $response = $this->get(route('attendance.list', [
            'month' => $addMonth
        ]));

        $response->assertSee($addMonth);
    }

    //９ー５詳細を押下すると、その日の勤怠詳細画面に遷移する
    public function test_user_attendance_list_detail()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        Carbon::setTestNow('2025-11-1 09:00:00');
        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);
        Carbon::setTestNow('2025-11-1 12:00:00');
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_start',
        ]);
        Carbon::setTestNow('2025-11-1 13:00:00');
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_end',
        ]);
        Carbon::setTestNow('2025-11-1 18:00:00');
        $response = $this->post(route('attendance.update'), [
            'action' => 'end',
        ]);

        $attendance = \App\Models\Attendance::where('user_id', $user->id)
            ->whereDate('work_date', '2025-11-01')
            ->first();


        $this->get('/attendance/list');

        $response = $this->get(route('attendance.detail', ['id' => $attendance->id]));

        $response->assertSee('勤怠詳細');
    }
}
