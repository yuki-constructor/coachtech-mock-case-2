<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ===================================================
 *  （テスト項目）
 *  ID:2 ログイン認証機能（一般ユーザー）
 * ===================================================
 */
class EmployeeLoginTest extends TestCase
{
    // データベーストランザクションを利用
    use DatabaseTransactions;

    // ルートをプロパティとして定義
    // protected string $route = '/employee/register';
    // protected string $route;
    protected string $registerRoute;
    protected string $loginRoute;
    protected string $authenticateRoute;

    protected function setUp(): void
    {
        parent::setUp();

        // $this->route = route('employee.register');
        $this->registerRoute = route('employee.register');
        $this->loginRoute = route('employee.login');
        $this->authenticateRoute = route('employee.authenticate');
    }

    /**
     * employeeユーザーを登録する共通メソッド
     */
    protected function registerEmployee(): void
    {
        // employeeユーザーの登録
        $this->post($this->registerRoute, [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
    }

    /**
     * ログインページを開き、ステータスコードを確認する共通メソッド
     */
    protected function visitLoginPage(): void
    {
        // employeeユーザーのログインページを開く
        $response = $this->get($this->loginRoute);

        // ステータスコード 200 を確認
        $response->assertStatus(200);
    }

    /**
     * メールアドレスが未入力の場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    #[Test]
    public function test_email_is_required_on_login()
    {
        // ユーザー登録
        // $response = $this->post($this->route, [
        // $response = $this->post($this->registerRoute, [
        //     'name' => 'テストユーザー',
        //     'email' => 'test@example.com',
        //     'password' => 'password123',
        //     'password_confirmation' => 'password123',
        // ]);
        $this->registerEmployee();

        // employeeユーザーのログインページを開く
        // $response = $this->get($this->loginRoute);
        $this->visitLoginPage();

        // ステータスコード 200 を確認
        // $response->assertStatus(200);

        // ログイン時にメールアドレスを空にする
        $response = $this->followingRedirects()->post($this->authenticateRoute, [
            'email' => '',
            'password' => 'password123',
        ]);

        // バリデーションエラーを確認
        $response->assertSee('メールアドレスを入力してください');
    }

    /**
     * パスワードが未入力の場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    #[Test]
    public function test_password_is_required_on_login()
    {
        // ユーザー登録
        // $response = $this->post($this->registerRoute, [
        //     'name' => 'テストユーザー',
        //     'email' => 'test@example.com',
        //     'password' => 'password123',
        //     'password_confirmation' => 'password123',
        // ]);
        $this->registerEmployee();

        // employeeユーザーのログインページを開く
        // $response = $this->get($this->loginRoute);
        // $this->visitLoginPage();

        // ステータスコード 200 を確認
        // $response->assertStatus(200);

        // ログイン時にパスワードを空にする
        $response = $this->post($this->authenticateRoute, [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        // バリデーションエラーを確認
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /**
     * 登録内容と一致しない場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    #[Test]
    public function test_login_fails_with_incorrect_credentials()
    {
        // ユーザー登録
        // $response = $this->post($this->registerRoute, [
        //     'name' => 'テストユーザー',
        //     'email' => 'test@example.com',
        //     'password' => 'password123',
        //     'password_confirmation' => 'password123',
        // ]);
        $this->registerEmployee();

        // employeeユーザーのログインページを開く
        // $response = $this->get($this->loginRoute);
        $this->visitLoginPage();

        // ステータスコード 200 を確認
        // $response->assertStatus(200);

        // 誤ったメールアドレスでログイン
        $response = $this->post($this->authenticateRoute, [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);

        // セッションにエラーメッセージがあるか確認
        $response->assertSessionHas('error', 'ログイン情報が登録されていません。');
    }
}
