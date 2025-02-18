<?php

use Illuminate\Support\Facades\Route;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\EmployeeController;

// Route::get('/employee/email-authentication-invitation', function () {
//     return view('auth/employee/email-authentication-invitation');
// });

// ===================================================
//  従業員ユーザーの認証関連
// ===================================================

// 従業員ユーザーのログイン画面・登録画面
Route::prefix('employee')->group(function () {

    Route::get('/login', [EmployeeController::class, 'login'])->name('employee.login'); // 従業員のログインページ

    Route::get('/register', [EmployeeController::class, 'register'])->name('employee.register'); // 従業員のログインページ
});

// 従業員ユーザーの登録処理
Route::post('/employee/register', [EmployeeController::class, 'store']);

// メール認証処理
Route::get('/email/verify/{id}/{hash}', [EmployeeController::class, 'emailVerify'])->name('verification.verify');

//メール認証誘導画面表示
Route::get('/employee/email-authentication-invitation/{employeeId}', [EmployeeController::class, 'invitation'])->name('email.authentication.invitation');

// 認証メール再送処理
Route::post('/email/verification-notification/{employeeId}', [EmployeeController::class, 'resend'])
    ->name('verification.resend');

// 従業員ユーザーの勤怠登録画面表示
Route::get('/employee/attendance-create/{employeeId}', [EmployeeController::class, 'attendanceCreate'])
    ->name('employee.attendance.create');
