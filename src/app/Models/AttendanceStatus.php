<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class AttendanceStatus extends Model
{
    use HasFactory;

    protected $fillable = ['status'];

    // AttendanceStatusは1対多の関係でAttendanceと関連（従業員の勤怠ステータス）
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
