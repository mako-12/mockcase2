<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AttendanceRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;
    // use DatabaseMigrations;

    protected function setUp(): void
    {
        // \Carbon\Carbon::setLocale('ja');
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    //勤怠詳細画面の「名前」がログインユーザーの氏名になっている
    public function test_attendance_detail_get_name()
    {
        $user = User::find(2);

        $response = $this->actingAs($user)->get('/attendance/detail/1');

        $response->assertSee('田中　一郎');
    }

    //１０－２勤怠詳細画面の「日付」が選択した日付になっている
    public function test_attendance_detail_get_date()
    {
        $user = User::factory()->create([
            'name' => '田中 太郎',
            'email' => 'tarou@example.com',
            'email_verified_at' => now(),
        ]);


        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'start_time' => '2025-12-01 09:00:00',
            'end_time' => '2025-12-01 18:00:00',
            'status' => 0,
        ]);

        $break = \App\Models\BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '2025-12-01 12:00:00',
            'break_end' => '2025-12-01 13:00:00',
        ]);

        $this->actingAs($user)->get('/attendance/list');

        // $attendance = \App\Models\Attendance::where('user_id', $user->id)
        //     ->whereDate('work_date', '2025-11-01')
        //     ->first();

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', ['id' => $attendance->id]));

        $response->assertStatus(200);

        $dateYear = \Carbon\Carbon::parse($attendance->work_date)->format($attendance->work_date->format('Y'));
        $dateDay = \Carbon\Carbon::parse($attendance->work_date)->format($attendance->work_date->format('n月j日'));


        $response->assertSee($dateYear);
        $response->assertSee($dateDay);
    }

    //１０－３「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致している
    public function test_attendance_detail_get_start_end_time()
    {
        $user = User::factory()->create([
            'name' => '田中 太郎',
            'email' => 'tarou@example.com',
            'email_verified_at' => now(),
        ]);


        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'start_time' => '2025-12-01 09:00:00',
            'end_time' => '2025-12-01 18:00:00',
            'status' => 0,
        ]);

        $break = \App\Models\BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '2025-12-01 12:00:00',
            'break_end' => '2025-12-01 13:00:00',
        ]);

        $this->actingAs($user)->get('/attendance/list');

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', ['id' => $attendance->id]));

        $response->assertStatus(200);

        $startTime = \Carbon\Carbon::parse($attendance->start_time)->format($attendance->start_time->format('H:i'));
        $endTime = \Carbon\Carbon::parse($attendance->end_time)->format($attendance->end_time->format('H:i'));


        $response->assertSee($startTime);
        $response->assertSee($endTime);
    }

    //「休憩」に記されている時間がログインユーザーの打刻と一致している
    public function test_attendance_detail_get_break_time()
    {
        $user = User::factory()->create([
            'name' => '田中 太郎',
            'email' => 'tarou@example.com',
            'email_verified_at' => now(),
        ]);


        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'start_time' => '2025-12-01 09:00:00',
            'end_time' => '2025-12-01 18:00:00',
            'status' => 0,
        ]);

        $break = \App\Models\BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '2025-12-01 12:00:00',
            'break_end' => '2025-12-01 13:00:00',
        ]);

        $this->actingAs($user)->get('/attendance/list');

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', ['id' => $attendance->id]));

        $response->assertStatus(200);

        $breakStart = \Carbon\Carbon::parse($break->break_start)->format('H:i');

        $breakEnd = \Carbon\Carbon::parse($break->break_end)->format('H:i');


        $response->assertSee($breakStart);
        $response->assertSee($breakEnd);
    }

    //出勤時間が退勤時間よりも後になっている場合、エラーメッセージが表示される
    public function test_attendance_detail_validate_start_time()
    {
        $user = User::factory()->create([
            'name' => '田中 太郎',
            'email' => 'tarou@example.com',
            'email_verified_at' => now(),
        ]);


        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'start_time' => '2025-12-01 09:00:00',
            'end_time' => '2025-12-01 18:00:00',
            'status' => 0,
        ]);

        $break = \App\Models\BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '2025-12-01 12:00:00',
            'break_end' => '2025-12-01 13:00:00',
        ]);

        $this->actingAs($user)->get('/attendance/list');

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', ['id' => $attendance->id]));


        $startTime = \Carbon\Carbon::parse('2025-12-01 18:00:00')->format('H:i');
        $endTime = \Carbon\Carbon::parse('2025-12-01 09:00:00')->format('H:i');


        $response = $this->post(
            route('attendance.request.submit', ['id' => $attendance->id]),
            [
                'start_time' => $startTime,
                'end_time' => $endTime,
            ]
        );

        $response->assertSessionHasErrors([
            'end_time' => '出勤時間もしくは退勤時間が不適切な値です',
        ]);
    }

    //１１－２休憩開始時間が退勤時間よりも後になっている場合、エラーメッセージが表示される
    public function test_attendance_detail_validate_break_start_time()
    {
        $user = User::factory()->create([
            'name' => '田中 太郎',
            'email' => 'tarou@example.com',
            'email_verified_at' => now(),
        ]);

        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'start_time' => '2025-12-01 09:00:00',
            'end_time' => '2025-12-01 18:00:00',
            'total_work_time' => 480,
            'status' => 0,
        ]);

        $break = \App\Models\BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '2025-12-01 12:00:00',
            'break_end' => '2025-12-01 13:00:00',
        ]);

        $this->actingAs($user)->get('/attendance/list');

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', ['id' => $attendance->id]));

        $startTime = \Carbon\Carbon::parse($attendance->start_time)->format('H:i');
        $endTime = \Carbon\Carbon::parse($attendance->end_time)->format('H:i');
        $breakStart = \Carbon\Carbon::parse('2025-12-01 08:00:00')->format('H:i');
        $breakEnd = \Carbon\Carbon::parse('2025-12-01 13:00:00')->format('H:i');

        $response = $this->actingAs($user)->post(
            route('attendance.request.submit', ['id' => $attendance->id]),
            [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'break_start' => [$breakStart],
                'break_end' => [$breakEnd],
                'reason' => '理由',
            ]
        );

        $response->assertStatus(302);
        // $response->assertRedirect(route('attendance.detail', ['id' => $attendance->id]));
        $response->assertSessionHasErrors(['break_start.0' => '休憩時間は不適切な値です',]);
    }




    //１１－３休憩終了時間が退勤時間より後になっている場合、エラーメッセージが表示される
    public function test_attendance_detail_validate_break_end_time()
    {
        $user = User::factory()->create([
            'name' => '田中 太郎',
            'email' => 'tarou@example.com',
            'email_verified_at' => now(),
        ]);

        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'start_time' => '2025-12-01 09:00:00',
            'end_time' => '2025-12-01 18:00:00',
            'total_work_time' => 480,
            'status' => 0,
        ]);

        $break = \App\Models\BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '2025-12-01 12:00:00',
            'break_end' => '2025-12-01 13:00:00',
        ]);

        $this->actingAs($user)->get('/attendance/list');

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', ['id' => $attendance->id]));


        $startTime = \Carbon\Carbon::parse($attendance->start_time)->format('H:i');
        $endTime = \Carbon\Carbon::parse($attendance->end_time)->format('H:i');
        $breakStart = \Carbon\Carbon::parse('2025-12-01 12:00:00')->format('H:i');
        $breakEnd = \Carbon\Carbon::parse('2025-12-01 19:00:00')->format('H:i');


        $response = $this->post(
            route('attendance.request.submit', ['id' => $attendance->id]),
            [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'break_start' => [$breakStart],
                'break_end' => [$breakEnd],
                'reason' => '理由',
            ]
        );

        $response->assertStatus(302);

        $response->assertSessionHasErrors(['break_end.0' => '休憩時間もしくは退勤時間が不適切な値です',]);
    }

    //備考欄が未入力の場合エラーメッセージが表示される
    public function test_attendance_detail_validate_reason()
    {
        $user = User::factory()->create([
            'name' => '田中 太郎',
            'email' => 'tarou@example.com',
            'email_verified_at' => now(),
        ]);

        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'start_time' => '2025-12-01 09:00:00',
            'end_time' => '2025-12-01 18:00:00',
            'total_work_time' => 480,
            'status' => 0,
        ]);

        $break = \App\Models\BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '2025-12-01 12:00:00',
            'break_end' => '2025-12-01 13:00:00',
        ]);

        $this->actingAs($user)->get('/attendance/list');

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', ['id' => $attendance->id]));


        $response = $this->post(
            route('attendance.request.submit', ['id' => $attendance->id]),
            [
                'reason' => '',
            ]
        );

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['reason' => '備考を記入してください',]);
    }

    //修正申請処理が実行される
    public function test_attendance_detail_fixes_request()
    {
        $user = User::factory()->create([
            'name' => '田中 太郎',
            'email' => 'tarou@example.com',
            'email_verified_at' => now(),
        ]);

        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'start_time' => '2025-12-01 09:00:00',
            'end_time' => '2025-12-01 18:00:00',
            'total_work_time' => 480,
            'status' => 0,
        ]);

        $break = \App\Models\BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '2025-12-01 12:00:00',
            'break_end' => '2025-12-01 13:00:00',
        ]);

        $this->actingAs($user)->get('/attendance/list');

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', ['id' => $attendance->id]));

        $response = $this->post(
            route('attendance.request.submit', ['id' => $attendance->id]),
            [
                'start_time' => '10:00',
                'end_time' => '19:00',
                'break_start' => ['13:00'],
                'break_end' => ['14:00'],
                'reason' => 'テスト',
            ]
        );

        $response->assertStatus(302);
        $follow = $this->followRedirects($response);
        $follow->assertSee('承認待ちのため修正はできません。');
    }

    //「承認待ち」にログインユーザーが行った申請がすべて表示されていること
    public function test_request_list_pending()
    {
        $user = User::factory()->create([
            'name' => '田中 太郎',
            'email' => 'tarou@example.com',
            'email_verified_at' => now(),
        ]);

        $attendance = \App\Models\Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'start_time' => '2025-12-01 09:00:00',
            'end_time' => '2025-12-01 18:00:00',
            'total_work_time' => 480,
            'status' => 0,
        ]);

        $break = \App\Models\BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '2025-12-01 12:00:00',
            'break_end' => '2025-12-01 13:00:00',
        ]);

        $this->actingAs($user)->get('/attendance/list');

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', ['id' => $attendance->id]));

        $response = $this->post(
            route('attendance.request.submit', ['id' => $attendance->id]),
            [
                'start_time' => '10:00',
                'end_time' => '19:00',
                'break_start' => ['13:00'],
                'break_end' => ['14:00'],
                'reason' => 'テスト',
            ]
        );

        $response->assertStatus(302);
        $request = AttendanceRequest::latest()->first();

        $this->assertDatabaseHas('attendance_requests', [
            'id' => $request->id,
            'status' => AttendanceRequest::STATUS_PENDING,
        ]);
    }


    //「承認済み」に管理者が承認した修正申請が全て表示されている
    public function test_request_list_approved()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = \App\Models\Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'start_time' => '2025-12-01 09:00:00',
            'end_time' => '2025-12-01 18:00:00',
            'total_work_time' => 480,
            'status' => 0,
        ]);

        $break = \App\Models\BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '2025-12-01 12:00:00',
            'break_end' => '2025-12-01 13:00:00',
        ]);

        $this->actingAs($user)->get('/attendance/list');

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', ['id' => $attendance->id]));

        $response = $this->post(
            route('attendance.request.submit', ['id' => $attendance->id]),
            [
                'start_time' => '10:00',
                'end_time' => '19:00',
                'break_start' => ['13:00'],
                'break_end' => ['14:00'],
                'reason' => 'テスト',
            ]
        );

        $response->assertStatus(302);
        $request = AttendanceRequest::latest()->first();

        AttendanceRequest::where('attendance_id', $attendance->id)
            ->update([
                'status' => AttendanceRequest::STATUS_APPROVED,
            ]);

        $this->assertDatabaseHas('attendance_requests', [
            'id' => $request->id,
            'status' => AttendanceRequest::STATUS_APPROVED,
        ]);
    }

    //各申請の「詳細」を押下すると勤怠詳細画面に遷移する
    public function test_attendance_detail_request_view_detail()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $attendance = \App\Models\Attendance::create([
            'user_id' => $user->id,
            'work_date' => '2025-12-01',
            'start_time' => '2025-12-01 09:00:00',
            'end_time' => '2025-12-01 18:00:00',
            'total_work_time' => 480,
            'status' => 0,
        ]);

        $break = \App\Models\BreakTime::factory()->create([
            'attendance_id' => $attendance->id,
            'break_start' => '2025-12-01 12:00:00',
            'break_end' => '2025-12-01 13:00:00',
        ]);

        $this->actingAs($user)->get('/attendance/list');

        $response = $this->actingAs($user)
            ->get(route('attendance.detail', ['id' => $attendance->id]));

        $response = $this->post(
            route('attendance.request.submit', ['id' => $attendance->id]),
            [
                'start_time' => '10:00',
                'end_time' => '19:00',
                'break_start' => ['13:00'],
                'break_end' => ['14:00'],
                'reason' => 'テスト',
            ]
        );

        $request = AttendanceRequest::latest()->first();

        $this->get(route('general.attendance.request'));

        $response = $this->get(route('attendance.detail', ['id' => $request->attendance->id]));

        $response->assertStatus(200);
        $response->assertSee('勤怠詳細');
    }
}
