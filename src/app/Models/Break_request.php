<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Break_request extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_request_id',
        'break_id',
        'requested_break_start',
        'requested_break_start',
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
