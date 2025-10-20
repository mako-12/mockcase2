<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\BreakTime;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Database\Seeders\UsersTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(UsersTableSeeder::class);


        //ダミーデータの記述
        // 一般ユーザーだけを対象にする（id:2〜3）
        $users = User::where('role', 0)->get();

        // 3ヶ月分（先月・今月・来月）
        $months = [
            Carbon::now()->subMonth(),
            Carbon::now(),
            Carbon::now()->addMonth(),
        ];

        foreach ($users as $user) {
            foreach ($months as $month) {
                $daysInMonth = $month->daysInMonth;

                // ランダムに5日間休みに設定
                $holidays = collect(range(1, $daysInMonth))
                    ->random(5)
                    ->toArray();

                for ($day = 1; $day <= $daysInMonth; $day++) {
                    // 休みの日はスキップ
                    if (in_array($day, $holidays)) continue;

                    $date = $month->copy()->day($day);

                    // 出勤開始・終了時間
                    $start = $date->copy()->setTime(9, 0);
                    $end = $date->copy()->setTime(18, 0);

                    // 休憩（12:00〜13:00）
                    $breakStart = $date->copy()->setTime(12, 0);
                    $breakEnd = $date->copy()->setTime(13, 0);

                    // 勤務時間計算（9時間 - 休憩1時間 = 8時間）
                    $totalWorkMinutes = $end->diffInMinutes($start) - $breakEnd->diffInMinutes($breakStart);

                    // 出勤データ作成
                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'work_date' => $date->format('Y-m-d'),
                        'start_time' => $start,
                        'end_time' => $end,
                        'total_work_time' => $totalWorkMinutes, // 分単位で登録
                        'status' => 3, // 退勤済み
                    ]);

                    // 休憩データ作成
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $breakStart,
                        'break_end' => $breakEnd,
                    ]);
                }
            }
        }
    }
}
