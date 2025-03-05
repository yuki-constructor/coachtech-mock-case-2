<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRequestBreak extends Model
{
    protected $fillable = ['attendance_request_id', 'break_id', 'break_start_time', 'break_end_time'];

    protected $casts = [
        'break_start_time' => 'datetime',
        'break_end_time' => 'datetime',
    ];

    // AttendanceRequestBreakは１対多の関係でAttendanceRequestと関連（一つの attendance_requestsテーブルのidに紐づくattendance_request_breaksテーブルのレコードが複数存在する場合がある）
    public function attendanceRequest()
    {
        return $this->belongsTo(AttendanceRequest::class);
    }
}
