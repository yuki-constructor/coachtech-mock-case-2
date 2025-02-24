<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * ==============================
     * 管理員ユーザーの認証関連
     * ==============================
     */

    /**
     * 管理者のログイン画面を表示
     *
     * @route GET /admin/login
     * @return \Illuminate\View\View
     */
    public function login()
    {
        return view('auth.admin.login');
    }

    /**
     * 管理者のログイン認証処理
     *
     * @route POST /admin/login
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate(LoginRequest $loginRequest)
    {
        // 認証情報を取得
        $credentials = $loginRequest->only('email', 'password');

        // 認証処理
        if (Auth::guard('admin')->attempt($credentials)) {
            $loginRequest->session()->regenerate();

            // 勤怠一覧画面（管理者）にリダイレクト
            return redirect()->route('attendance.list');
        }

        return to_route('admin.login')->with(['error' => 'ログイン情報が登録されていません。']);
    }

    /**
     * 管理者のログアウト処理
     *
     * @route POST /admin/logout
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * 管理者の勤怠リスト画面を表示
     *
     * @route GET /attendance-list
     * @return \Illuminate\View\View
     */
    public function attendanceList()
    {
        return view('auth.admin.attendance-list');
    }
}
