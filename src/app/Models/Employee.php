<?php

namespace App\Models;

use App\Mail\CustomVerificationEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;



class Employee extends Authenticatable implements MustVerifyEmail
// MustVerifyEmailをモデルに追加し、メール認証の機能を有効化
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Employeeは1対多の関係でAttendanceと関連(従業員の勤怠情報）
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // 認証メール送信処理
    public function sendEmailVerificationNotification()
    {
        Mail::to($this->email)->send(new CustomVerificationEmail($this));
    }
}
