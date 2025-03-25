<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ===================================================
 *  （テスト項目）
 *  ID:3 ログイン認証機能（管理者）
 * ===================================================
 */
class AdminLoginTest extends TestCase
{
    // データベーストランザクションを利用
    use DatabaseTransactions;

    // ルートをプロパティとして定義
    protected string $loginRoute;
    protected string $authenticateRoute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loginRoute = route('admin.login');
        $this->authenticateRoute = route('admin.authenticate');
    }

    /**
     * ログインページを開き、ステータスコードを確認する共通メソッド
     */
    protected function visitLoginPage()
    {
        // adminユーザーのログインページを開く
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
        // adminユーザーのログインページを開く
        $this->visitLoginPage();

        // ログイン時にメールアドレスを空にする
        $response = $this->followingRedirects()->post($this->authenticateRoute, [
            'email' => '',
            'password' => 'password123',
        ]);

        // バリデーションメッセージを確認
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
        // adminユーザーのログインページを開く
        $this->visitLoginPage();

        // ログイン時にパスワードを空にする
        $response = $this->followingRedirects()->post($this->authenticateRoute, [
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        // バリデーションメッセージを確認
        $response->assertSee('パスワードを入力してください');
    }

    /**
     * 誤ったログイン情報を入力の場合、エラーメッセージが表示される
     *
     * @return void
     */
    #[Test]
    public function test_login_fails_with_incorrect_credentials()
    {
        // adminユーザーのログインページを開く
        $this->visitLoginPage();

        // 誤ったメールアドレスでログイン
        $response = $this->followingRedirects()->post($this->authenticateRoute, [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);

        // エラーメッセージを確認
        $response->assertSee('ログイン情報が登録されていません。');
    }
}
