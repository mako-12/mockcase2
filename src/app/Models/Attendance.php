<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    public const STATUS_OFF_DUTY = 0;
    public const STATUS_WORKING = 1;
    public const STATUS_BREAK = 2;
    public const STATUS_FINISHED = 3;

    protected $fillable = [
        'user_id',
        'work_date',
        'start_time',
        'end_time',
        'total_work_time',
        'status',
    ];

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_OFF_DUTY => '勤務外',
            self::STATUS_WORKING => '出勤中',
            self::STATUS_BREAK => '休憩中',
            self::STATUS_FINISHED => '退勤済み',
            default => '不明',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }


    //休憩時間の合計を計算(分単位)
    public function getBreakDurationAttribute(): int
    {
        if ($this->breakTimes->isEmpty()) return 0;

        $totalBreakMinutes = 0;

        foreach ($this->breakTimes as $breakTime) {
            $start = Carbon::parse($breakTime->break_start);
            $end = Carbon::parse($breakTime->break_end);

            $totalBreakMinutes += $end->diffInMinutes($start);
        }
        return $totalBreakMinutes;
    }

    //休憩時間を「H:i」形式で返す(時間:分)
    public function getBreakDurationFormatAttribute(): string
    {
        $minutes = $this->break_duration;

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return sprintf('%d:%02d', $hours, $remainingMinutes);
    }

    // * 実勤務時間を計算(分単位)
    //  * = 出勤～退勤の時間 - 休憩時間
    public function getWorkDurationAttribute(): int
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        $totalMinutes = $end->diffInMinutes($start);
        $breakMinutes = $this->break_duration;

        return $totalMinutes - $breakMinutes;
    }

    //実勤務時間を「H:i」形式で返す(時間:分)
    public function getWorkDurationFormatAttribute(): string
    {
        $minutes = $this->work_duration;

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return sprintf('%d:%02d', $hours, $remainingMinutes);
    }
}
