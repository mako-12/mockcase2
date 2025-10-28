<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_request_id',
        'break_time_id',
        'requested_break_start',
        'requested_break_end',
    ];

    protected $casts = [
        'requested_break_start' => 'datetime',
        'requested_break_end' => 'datetime',
    ];

    public function attendanceRequest()
    {
        return $this->belongsTo(AttendanceRequest::class);
    }

    public function breakTime()
    {
        return $this->belongsTo(BreakTime::class);
    }
}
