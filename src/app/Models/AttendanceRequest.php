<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    protected $fillable = ['attendance_id', 'attendance_request_status_id', 'start_time', 'end_time', 'reason'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // AttendanceRequestは１対多の関係でAttendanceと関連（修正申請は何度でもできる）
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    // AttendanceRequestは１対多の関係でAttendanceRequestStatusと関連（従業員の勤怠修正申請ステータス）
    public function attendanceRequestStatus()
    {
        return $this->belongsTo(AttendanceRequestStatus::class, 'attendance_request_status_id');
    }

    // AttendanceRequest は１対多の関係でAttendanceRequestBreakと関連（一つのattendance_requestsテーブルのidに紐づく attendance_request_breaksテーブルのレコードが複数存在する場合がある）
    public function attendanceRequestBreaks()
    {
        return $this->hasMany(AttendanceRequestBreak::class);
    }
}
