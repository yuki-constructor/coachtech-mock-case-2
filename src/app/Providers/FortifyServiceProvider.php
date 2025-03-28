<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // ビューの分離
        // 従業員用
        Fortify::viewPrefix('auth.employee.');
        // 管理者用
        Fortify::viewPrefix('auth.admin.');

        // カスタム認証処理(ログイン処理)
        Fortify::authenticateUsing(function (Request $request) {
            if ($request->is('admin/*')) {
                $admin = Admin::where('email', $request->email)->first();
                if ($admin && Hash::check($request->password, $admin->password)) {
                    Auth::guard('admin')->login($admin);
                    return $admin;
                }
            } else {
                $employee = Employee::where('email', $request->email)->first();

                if (!$employee || !Hash::check($request->password, $employee->password)) {
                    return null;
                }

                // メール認証が完了していない場合
                if (!$employee->hasVerifiedEmail()) {
                    session()->flash('status', 'メール認証を完了してください。');
                    session()->flash('resend_verification_email', true); // 再送ボタンを表示
                    return null;
                }

                Auth::guard('employee')->login($employee);
                return $employee;
            }
        });

        // ユーザー登録
        Fortify::createUsersUsing(CreateNewUser::class);

        // メール認証誘導画面
        Fortify::verifyEmailView(function () {
            return view('auth.employee.verify-email'); // 認証メール送信後の画面
        });
    }
}
