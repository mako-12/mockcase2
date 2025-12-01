<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceTest extends TestCase
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

    //現在の日時情報がUIと同じ形式で出力されてるか
    public function test_attendance_user_time_format()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $response = $this->get('/attendance');
        $today = \Carbon\Carbon::today()->isoFormat('YYYY年MM月DD日(ddd)');
        $nowTime = \Carbon\Carbon::now()->format('H:i');

        $response->assertSee($today);
        $response->assertSee($nowTime);
    }

    //ステータス「勤務外」を表示
    public function test_attendance_user_status_off_day()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);
        $response = $this->get('/attendance');

        $response->assertSee('勤務外');
    }

    //ステータス「出勤中」を表示
    public function test_attendance_user_status_working()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    //ステータス「休憩中」を表示
    public function test_attendance_user_status_break()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_start',
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
    }

    //ステータス「退勤済」を表示
    public function test_attendance_user_status_finished()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_end',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'end',
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
    }

    //出勤ボタンが正しく機能する
    public function test_attendance_user_working_function()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertSee('出勤');

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => \App\Models\Attendance::STATUS_WORKING
        ]);
    }


    //出勤は一日一回のみできる
    public function test_attendance_user_working_one_time_only()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);


        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_end',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'end',
        ]);

        Auth::logout();
        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
        $response->assertSee('お疲れ様でした。');
        $response->assertDontSee('<button>出勤</button>');
    }

    //出勤時刻が勤怠一覧画面で確認できる
    public function test_attendance_user_working_time_detail_list()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);
        $nowTime = \Carbon\Carbon::now()->format('H:i');

        $response = $this->get('/attendance/list');

        $date = $nowTime = \Carbon\Carbon::now()->isoFormat('MM/DD(ddd)');
        $response->assertSee($nowTime);
    }

    //休憩ボタンが正しく機能する
    public function test_attendance_user_break_start_time()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩入');
    }


    //休憩は一日に何回もできる
    public function test_attendance_user_break_start_how_many_times()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_end',
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩入');
    }

    //休憩戻ボタンが正しく機能する
    public function test_attendance_user_break_end_time()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_end',
        ]);

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => \App\Models\Attendance::STATUS_WORKING
        ]);
    }

    //休憩戻は一日に何回でもできる
    public function test_attendance_user_break_end_how_many_times()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_end',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_start',
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');
    }

    //７－５休憩時刻が勤怠一覧画面で確認できる
    public function test_attendance_user_break_time_detail_list()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);
        $response = $this->post(route('attendance.update'), [
            'action' => 'break_start',
        ]);
        $breakStartTime = \Carbon\Carbon::now()->format('H:i');

        $response = $this->post(route('attendance.update'), [
            'action' => 'break_end',
        ]);
        $breakEndTime = \Carbon\Carbon::now()->format('H:i');

        $date = $breakStartTime = \Carbon\Carbon::now()->isoFormat('MM/DD(ddd)');

        $response = $this->get('/attendance/list');

        $response->assertSee($date);
        $response->assertSee($breakStartTime);
        $response->assertSee($breakEndTime);
    }


    //退勤ボタンが正しく機能する
    public function test_attendance_user_finished_time()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(), // ← これが超重要！
        ]);
        $this->actingAs($user);

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('退勤');

        $response = $this->post(route('attendance.update'), [
            'action' => 'end',
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('お疲れ様でした。');
    }

    //８－２退勤時刻が勤怠一覧画面で確認できる
    public function test_attendance_user_finished_time_detail_list()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $response = $this->post(route('attendance.update'), [
            'action' => 'start',
        ]);
        $startNow = \Carbon\Carbon::now()->format('H:i');

        $response = $this->post(route('attendance.update'), [
            'action' => 'end',
        ]);
        $endNow = \Carbon\Carbon::now()->format('H:i');

        $date = $startNow = \Carbon\Carbon::now()->isoFormat('MM/DD(ddd)');

        $response = $this->get('/attendance/list');
        $response->assertSee($date);
        $response->assertSee($startNow);
        $response->assertSee($endNow);
    }
}
