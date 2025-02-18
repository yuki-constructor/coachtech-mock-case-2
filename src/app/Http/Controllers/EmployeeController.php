<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Mail\CustomVerificationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\EmployeeRegisterRequest;

class EmployeeController extends Controller
{
    // ===================================================
    //  従業員ユーザーの認証関連
    // ===================================================

    // ---- ユーザー登録関連

    // 従業員ユーザーの登録画面表示
    public function register()
    {
        return view('auth.employee.register');
    }

    // 従業員ユーザーの登録・認証メール送信処理
    public function store(EmployeeRegisterRequest $request)
    {
        // ユーザー作成
        $employee = Employee::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
        ]);

        // 直接Mailableクラス指定
        // Mail::to($employee->email)->send(new CustomVerificationEmail($employee));

        // Employeeモデル記載のメソッド使用
        $employee->sendEmailVerificationNotification();

        // メール認証誘導画面ヘリダイレクト
        // return view('auth.employee.email-authentication-invitation', ['employee' => $employee]);
        return redirect()->route('email.authentication.invitation', ["employeeId" => $employee->id]);
    }

    // メール認証処理
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

    // メール認証誘導画面表示
    public function invitation($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        return view('auth.employee.email-authentication-invitation', ['employee' => $employee]);
    }

    // 認証メール再送処理
    public function resend($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        // 直接Mailableクラス指定
        // Mail::to($employee->email)->send(new CustomVerificationEmail($employee));

        // Employeeモデル記載のメソッド使用
        $employee->sendEmailVerificationNotification();

        return view('auth.employee.email-authentication-invitation', ['employee' => $employee]);
    }

    // 勤怠登録画面表示
    public function attendanceCreate($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        return view('auth.employee.attendance-create', ['employee' => $employee]);
    }


    // ---- ログイン関連

    // 従業員ユーザーの登録画面表示
    public function login()
    {
        return view('auth.employee.login');
    }



}
