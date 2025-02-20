<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\EmployeeRegisterRequest;

class EmployeeController extends Controller
{
    /**
     * ==============================
     * 従業員ユーザーの認証関連
     * ==============================
     */

    /**
     * 従業員の登録画面を表示
     *
     * @return \Illuminate\View\View
     */
    public function register()
    {
        return view('auth.employee.register');
    }

    /**
     * 従業員の登録処理（認証メール送信処理も）
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(EmployeeRegisterRequest $request)
    {
        // ユーザー作成
        $employee = Employee::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        // Employeeモデル記載のメソッド使用
        $employee->sendEmailVerificationNotification();

        // メール認証誘導画面ヘリダイレクト
        return redirect()->route('email.authentication.invitation', ["employeeId" => $employee->id]);
    }

    /**
     * 従業員のメール認証処理
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function emailVerify(Request $request, $id, $hash)
    {
        $user = Employee::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect()->route('verification.notice')->with('error', '無効な認証リンクです。');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->route('employee.attendance.create', ["employeeId" => $user->id]);
    }

    /**
     * 従業員のメール認証誘導画面表示
     *
     * @return \Illuminate\View\View
     */
    public function invitation($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        return view('auth.employee.email-authentication-invitation', ['employee' => $employee]);
    }

    /**
     * 従業員の認証メール再送処理
     *
     * @return \Illuminate\View\View
     */
    public function resend($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        // Employeeモデル記載のメソッド使用
        $employee->sendEmailVerificationNotification();

        return view('auth.employee.email-authentication-invitation', ['employee' => $employee]);
    }

    /**
     * 従業員の勤怠登録画面を表示
     *
     * @return \Illuminate\View\View
     */
    public function attendanceCreate($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        return view('auth.employee.attendance-create', ['employee' => $employee]);
    }

    /**
     * 従業員のログイン画面を表示
     *
     *  @return \Illuminate\View\View
     */
    public function login()
    {
        return view('auth.employee.login');
    }
}
