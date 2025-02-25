<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


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
     * @route GET /employee/register
     * @return \Illuminate\View\View
     */
    public function register()
    {
        return view('auth.employee.register');
    }

    /**
     * 従業員の登録処理（認証メール送信処理も）
     *
     * @route POST /employee/register
     * @param EmployeeRegisterRequest $request
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

        // 認証メール送信（Employeeモデル記載のメソッド使用）
        $employee->sendEmailVerificationNotification();

        // メール認証誘導画面ヘリダイレクト
        return redirect()->route('email.authentication.invitation', ["employeeId" => $employee->id]);
    }

    /**
     * 従業員のメール認証処理
     *
     * @route GET /email/verify/{id}/{hash}
     * @param Request $request
     * @param int $id
     * @param string $hash
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

        // 認証後、自動でログイン
        Auth::guard('employee')->login($user);

        // 勤怠登録画（従業員）へリダイレクト
        return redirect()->route('employee.attendance.create');
    }

    /**
     * 従業員のメール認証誘導画面表示
     *
     * @route GET /email-authentication-invitation/{employeeId}
     * @param int $employeeId
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
     * @route POST /email/verification-notification/{employeeId}
     * @param int $employeeId
     * @return \Illuminate\View\View
     */
    public function resend($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);

        // 認証メール送信（Employeeモデル記載のメソッド使用）
        $employee->sendEmailVerificationNotification();

        return view('auth.employee.email-authentication-invitation', ['employee' => $employee]);
    }

    /**
     * 従業員のログイン画面を表示
     *
     * @route GET /employee/login
     * @return \Illuminate\View\View
     */
    public function login()
    {
        return view('auth.employee.login');
    }

    /**
     * 従業員のログイン認証処理
     *
     * @route POST /employee/login
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(LoginRequest $loginRequest)
    {
        // 認証情報を取得
        $credentials = $loginRequest->only('email', 'password');

        // 認証処理
        if (Auth::guard('employee')->attempt($credentials)) {

            // 早期リターン
            return to_route('employee.login')->with(['error' => 'ログイン情報が登録されていません。']);
        }
        $loginRequest->session()->regenerate();

        $employee = Auth::guard('employee')->user();

        // メール認証未完了の場合
        if (!$employee->email_verified_at) {

            // ログアウト
            Auth::guard('employee')->logout();

            // 認証メール送信（Employeeモデル記載のメソッド使用）
            $employee->sendEmailVerificationNotification();

            // メール認証誘導画面へリダイレクト
            return redirect()->route('email.authentication.invitation', ['employeeId' => $employee->id])
                ->with(['error' => 'メール認証が未完了です。メール認証を完了してください。']);
        }

        // メール認証完了の場合
        // 勤怠登録画（従業員）へリダイレクト
        return redirect()->route('employee.attendance.create');
    }

    /**
     * 従業員のログアウト処理
     *
     * @route POST /employee/logout
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('employee')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('employee.login');
    }

    /**
     * 従業員の勤怠登録画面を表示
     *
     * @route GET /employee/attendance-create/{employeeId}
     * @param int $employeeId
     * @return \Illuminate\View\View
     */
    public function attendanceCreate()
    {
        $employee = auth('employee')->user();

        return view('auth.employee.attendance-create', ['employee' => $employee]);
    }
}
