<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRequestStatus extends Model
{
    protected $fillable = ['request_ status'];

    // 日本語ステータスを定数定義
    public const STATUS_APPROVED = '承認済み';
    public const STATUS_PENDING_APPROVAL = '承認待ち';

    // AttendanceRequestStatusは1対多の関係でAttendanceRequestと関連（従業員の勤怠修正申請ステータス）
    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }
}
