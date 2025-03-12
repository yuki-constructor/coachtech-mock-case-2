<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'admin_id',
        'original_start_time',
        'original_end_time',
        'corrected_start_time',
        'corrected_end_time',
        'reason',
    ];

    /**
     *  AttendanceCorrectionは１対多の関係でAttendanceと関連（管理者による修正は何度でもできる）
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     *  AttendanceCorrectionは１対多の関係でAdminと関連（管理者による修正は何度でもできる）
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     *  AttendanceCorrection は１対多の関係でAttendanceCorrectionBreakと関連（一つのattendance_correctionsテーブルのidに紐づく attendance_correction_breaksテーブルのレコードが複数存在する場合がある）
     */
    public function attendanceCorrectionBreaks()
    {
        return $this->hasMany(AttendanceCorrectionBreak::class, 'attendance_correction_id');
    }
}
