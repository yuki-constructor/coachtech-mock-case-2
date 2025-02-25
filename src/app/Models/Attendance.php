<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'date', 'start_time', 'end_time', 'attendance_status_id'];

    // Attendanceは１対多の関係でEmployeeと関連（従業員の勤怠情報）
    public function attendance()
    {
        return $this->belongsTo(Employee::class);
    }

    // Attendanceは１対多の関係でAttendanceStatusと関連（従業員の勤怠ステータス）
    public function status()
    {
        return $this->belongsTo(AttendanceStatus::class, 'attendance_status_id');
    }

    // Attendanceは１対多の関係でBreakと関連（従業員は１日に何度でも休憩できる）
    public function breaks()
    {
        return $this->hasMany(BreakModel::class);
    }
}
