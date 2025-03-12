<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRequestController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

/**
 * ==============================
 * 従業員ユーザーの認証関連
 * ==============================
 */

Route::prefix('employee')->group(function () {

    /**
     *  従業員のログイン画面を表示
     */
    Route::get('/login', [EmployeeController::class, 'login'])
        ->name('employee.login');

    /**
     *  従業員のログイン認証処理
     */
    Route::post('/login', [EmployeeController::class, 'authenticate'])
        ->name('employee.authenticate');

    /**
     *  従業員のログアウト処理
     */
    Route::post('/logout', [EmployeeController::class, 'logout'])
        ->name('employee.logout');

    /**
     * 従業員の登録画面を表示
     */
    Route::get('/register', [EmployeeController::class, 'register'])
        ->name('employee.register');

    /**
     * 従業員の登録処理
     */
    Route::post('/register', [EmployeeController::class, 'store'])
        ->name('employee.store');

    /**
     * 従業員のメール認証処理
     */
    Route::get('/email/verify/{id}/{hash}', [EmployeeController::class, 'emailVerify'])
        ->name('verification.verify');

    /**
     * メール認証誘導画面を表示
     */
    Route::get('/employee/email-authentication-invitation/{employeeId}', [EmployeeController::class, 'invitation'])
        ->name('email.authentication.invitation');

    /**
     * 認証メール再送処理
     */
    Route::post('/email/verification-notification/{employeeId}', [EmployeeController::class, 'resend'])
        ->name('verification.resend');

    /**
     * ==============================
     * 従業員ユーザーの勤怠登録関連
     * ==============================
     */
    Route::middleware('auth:employee')->group(function () {

        /**
         *  従業員の勤怠登録画面を表示（認証必須）
         */
        Route::get('/attendance-create', [AttendanceController::class, 'attendanceCreate'])
            ->name('employee.attendance.create');

        /**
         *  従業員の勤怠登録（メッセージ）画面を表示（認証必須）
         */
        Route::get('/attendance-message', [AttendanceController::class, 'attendanceMessage'])
            ->name('employee.attendance.message');

        /**
         *  従業員の出勤登録処理（認証必須）
         */
        Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])
            ->name('attendance.clock-in');

        /**
         *  従業員の休憩開始登録処理（認証必須）
         */
        Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart'])
            ->name('attendance.break-start');

        /**
         *  従業員の休憩終了登録処理（認証必須）
         */
        Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd'])
            ->name('attendance.break-end');

        /**
         *  従業員の退勤登録処理（認証必須）
         */
        Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])
            ->name('attendance.clock-out');

        /**
         *  従業員の勤怠一覧画面を表示（認証必須）
         */
        Route::get('/attendance-list', [AttendanceController::class, 'attendanceList'])
            ->name('employee.attendance.list');

        /**
         *  従業員の勤怠詳細画面を表示（認証必須）
         */
        Route::get('/attendance/{attendanceId}', [AttendanceController::class, 'attendanceShow'])
            ->name('employee.attendance.show');

        /**
         * ==============================
         * 従業員ユーザーの勤怠修正申請関連
         * ==============================
         */

        // 勤怠修正申請処理（認証必須）
        Route::post('/attendance/{attendanceId}/request', [AttendanceRequestController::class, 'attendanceRequest'])
            ->name('employee.attendance.request');

        /**
         *  従業員の勤怠修正申請一覧画面（承認待ち）を表示（認証必須）
         */
        Route::get('/attendance/request/list/pending', [AttendanceRequestController::class, 'attendanceRequestListPending'])
            ->name('employee.attendance.request.list.pending');
    });
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
    Route::get('/login', [AdminController::class, 'login'])
        ->name('admin.login');

    /**
     *  管理者のログイン認証処理
     */
    Route::post('/login', [AdminController::class, 'authenticate'])
        ->name('admin.authenticate');

    /**
     *  管理者のログアウト処理
     */
    Route::post('/logout', [AdminController::class, 'logout'])
        ->name('admin.logout');

    Route::middleware('auth:admin')->group(function () {

        /**
         *  管理者の勤怠リストを表示（認証必須）
         */
        Route::get('/attendance-list', [AdminController::class, 'attendanceList'])
            ->name('attendance.list');

        /**
         * ==============================
         * 管理者ユーザーの勤怠管理関連
         * ==============================
         */

        /**
         *  日次勤怠一覧画面（管理者用）を表示（認証必須）
         */
        Route::get('/attendance/daily-list/{date?}', [AttendanceController::class, 'attendanceDailyList'])
            ->name('admin.attendance.daily-list');

        /**
         *  勤怠詳細画面（管理者用）を表示（認証必須）
         */
        Route::get('/attendance/{attendanceId}/show', [AttendanceController::class, 'adminAttendanceShow'])
            ->name('admin.attendance.show');

        /**
         *  勤怠修正処理（管理者用）（認証必須）
         */
        Route::post('/attendance/{attendanceId}/correct', [AttendanceController::class, 'adminAttendanceCorrect'])
            ->name('admin.attendance.correct');

        /**
         *  従業員一覧画面（管理者用）を表示（認証必須）
         */
        Route::get('/attendance/employee-list', [AttendanceController::class, 'attendanceEmployeeList'])
            ->name('admin.attendance.employee-list');

        /**
         *  従業員別月次勤怠一覧画面（管理者用）を表示（認証必須）
         */
        Route::get('/attendance/monthly-list/{employeeId}', [AttendanceController::class, 'attendanceMonthlyList'])
            ->name('admin.attendance.monthly-list');

        /**
         *  従業員の勤怠修正申請一覧画面（承認待ち）（管理者用）を表示（認証必須）
         */
        Route::get('/attendance/request/list/pending', [AttendanceRequestController::class, 'adminAttendanceRequestListPending'])
            ->name('admin.attendance.request.list.pending');

        /**
         *  従業員の勤怠修正申請一覧画面（承認済み）（管理者用）を表示（認証必須）
         */
        Route::get('/attendance/request/list/approved', [AttendanceRequestController::class, 'adminAttendanceRequestListApproved'])
            ->name('admin.attendance.request.list.approved');

        /**
         *  修正申請承認画面を表示（認証必須）
         */
        Route::get('/attendance/request/{attendanceRequestId}/show', [AttendanceRequestController::class, 'adminAttendanceRequestShow'])
            ->name('admin.attendance.request.show');

        /**
         *  勤怠修正承認処理（管理者用）（認証必須）
         */
        Route::post('/attendance/request/{attendanceRequestId}/acknowledge', [AttendanceRequestController::class, 'attendanceRequestAcknowledge'])
            ->name('admin.attendance.request.acknowledge');
    });
});
