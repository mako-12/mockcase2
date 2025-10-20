<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'request_date',
        'request_start_time',
        'request_end_time',
        'reason',
        'status',
    ];

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => '承認待ち',
            self::STATUS_APPROVED => '承認済み',
            default => '不明',
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
