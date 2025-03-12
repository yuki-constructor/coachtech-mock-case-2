<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class AttendanceStatus extends Model
{
    use HasFactory;

    protected $fillable = ['status'];

    /**
     *  日本語ステータスを定数定義
     */
    public const STATUS_OFF = '勤務外';
    public const STATUS_ON = '勤務中';
    public const STATUS_BREAK = '休憩中';

    /**
     *  AttendanceStatusは1対多の関係でAttendanceと関連（従業員の勤怠ステータス）
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
