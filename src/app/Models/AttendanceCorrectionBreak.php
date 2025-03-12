<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionBreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_correction_id',
        'break_id',
        'original_break_start',
        'original_break_end',
        'corrected_break_start',
        'corrected_break_end',
    ];

    /**
     *  AttendanceCorrectionBreakは１対多の関係でAttendanceCorrectionと関連（一つの attendance_correctionsテーブルのidに紐づくattendance_correction_breaksテーブルのレコードが複数存在する場合がある）
     */
    public function attendanceCorrection()
    {
        return $this->belongsTo(AttendanceCorrection::class, 'attendance_correction_id');
    }

    /**
     *  AttendanceCorrectionBreakは１対多の関係でBreakModelと関連（一つの breaksテーブルのidに紐づくattendance_correction_breaksテーブルのレコードが複数存在する場合がある）
     */
    public function breakModel()
    {
        return $this->belongsTo(BreakModel::class, 'break_id');
    }
}
