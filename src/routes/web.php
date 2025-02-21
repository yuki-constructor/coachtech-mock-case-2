<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AdminController;

/**
 * ==============================
 * 従業員ユーザーの認証関連
 * ==============================
 */

Route::prefix('employee')->group(function () {

    /**
     *  従業員のログイン画面を表示
     */
    Route::get('/login', [EmployeeController::class, 'login'])->name('employee.login');

    /**
     * 従業員の登録画面を表示
     */
    Route::get('/register', [EmployeeController::class, 'register'])->name('employee.register');

    /**
     * 従業員の登録処理
     */
    Route::post('/register', [EmployeeController::class, 'store'])->name('employee.store');

    /**
     * 従業員のメール認証処理
     */
    Route::get('/email/verify/{id}/{hash}', [EmployeeController::class, 'emailVerify'])->name('verification.verify');

    /**
     * メール認証誘導画面を表示
     */
    Route::get('/employee/email-authentication-invitation/{employeeId}', [EmployeeController::class, 'invitation'])->name('email.authentication.invitation');

    /**
     * 認証メール再送処理
     */
    Route::post('/email/verification-notification/{employeeId}', [EmployeeController::class, 'resend'])
        ->name('verification.resend');

    /**
     *  従業員の勤怠登録画面を表示（認証必須）
     */
    Route::get('/employee/attendance-create/{employeeId}', [EmployeeController::class, 'attendanceCreate'])
        ->name('employee.attendance.create');
});

/**
 * ==============================
 * 管理者ユーザーの認証関連
 * ==============================
 */

Route::prefix('admin')->group(function () {

    /**
     *  管理者のログイン画面を表示
     */
    Route::get('/login', [AdminController::class, 'login'])->name('admin.login');

    /**
     *  管理者のログイン認証処理
     */
    Route::post('/login', [AdminController::class, 'authenticate'])->name('admin.authenticate');

    /**
     *  管理者のログアウト処理
     */
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth:admin')->group(function () {

        /**
         *  管理者の勤怠リストを表示（認証必須）
         */
        Route::get('/attendance-list', [AdminController::class, 'attendanceList'])->name('attendance.list');
    });
});
