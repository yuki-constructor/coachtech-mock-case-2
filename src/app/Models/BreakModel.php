<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakModel extends Model
{
    protected $table = 'breaks';
    protected $fillable = ['attendance_id', 'break_start_time', 'break_end_time'];

    /**
     *  Breakは１対多の関係でAttendanceと関連（従業員は１日に何度でも休憩できる）
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
