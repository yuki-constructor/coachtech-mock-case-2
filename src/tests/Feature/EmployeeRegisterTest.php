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
 *  ID:1 会員登録機能（一般ユーザー）
 * ===================================================
 */
class EmployeeRegisterTest extends TestCase
{
    // データベーストランザクションを利用
    use DatabaseTransactions;

    // ルートをプロパティとして定義
    // protected string $route = '/employee/register';
    protected string $route;

    protected function setUp(): void
    {
        parent::setUp();
        $this->route = route('employee.register');
    }

    /**
     * employeeユーザーの登録画面を開き、ステータスコードを確認する共通メソッド
     */
    protected function visitRegisterPage(): void
    {
        // employeeユーザーのユーザー登録画面を開く
        $response = $this->get($this->route);

        // ステータスコード 200 を確認
        $response->assertStatus(200);
    }

    /**
     * 名前が未入力の場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_name_is_required()
    {
        // employeeユーザーの登録ページを開く
        // $response = $this->get($this->route);
        $this->visitRegisterPage();

        // ステータスコード 200 を確認
        // $response->assertStatus(200);

        // 登録時に名前を空にする
        // $response = $this->post($this->route, [
        // $response = $this->followingRedirects()->post($this->route, [
        $response = $this->followingRedirects()->post($this->route, [
            // $response = $this->post(route('employee.store'), [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // $response->dump();
        // $response->dumpSession();


        // $response->assertRedirect(route('employee.register'));
        // $response->assertRedirect(url('/employee/register'));

        // // セッションのエラーメッセージを確認
        // $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);

        // 実際の画面上にエラーメッセージが表示されているかを確認
        $response->assertSeeText('お名前を入力してください');
    }


    /**
     * メールアドレスが未入力の場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_email_is_required()
    {
        // employeeユーザーの登録ページを開く
        // $response = $this->get($this->route);
        $this->visitRegisterPage();

        // ステータスコード 200 を確認
        // $response->assertStatus(200);

        // 登録時にメールアドレスを空にする
        // $response = $this->post($this->route, [
        $response = $this->followingRedirects()->post($this->route, [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        // セッションのエラーメッセージを確認
        // $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);

        // 実際の画面上にエラーメッセージが表示されているかを確認
        $response->assertSeeText('メールアドレスを入力してください');
    }

    /**
     * パスワードが8文字未満の場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_password_must_be_at_least_8_characters()
    {
        // employeeユーザーの登録ページを開く
        // $response = $this->get($this->route);
        $this->visitRegisterPage();

        // ステータスコード 200 を確認
        // $response->assertStatus(200);

        // 登録時にパスワードを８文字未満にする
        // $response = $this->post($this->route, [
        $response = $this->followingRedirects()->post($this->route, [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);
        // セッションのエラーメッセージを確認
        // $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);

        // 実際の画面上にエラーメッセージが表示されているかを確認
        $response->assertSeeText('パスワードは8文字以上で入力してください');
    }

    /**
     * パスワードが一致しない場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_password_must_be_confirmed()
    {
        // employeeユーザーの登録ページを開く
        // $response = $this->get($this->route);
        $this->visitRegisterPage();

        // ステータスコード 200 を確認
        // $response->assertStatus(200);

        // 登録時にパスワードと確認用パスワードを違うものにする
        // $response = $this->post($this->route, [
        $response = $this->followingRedirects()->post($this->route, [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]);
        // セッションのエラーメッセージを確認
        // $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);

        // 実際の画面上にエラーメッセージが表示されているかを確認
        $response->assertSeeText('パスワードと一致しません');
    }

    /**
     * パスワードが未入力の場合、バリデーションメッセージが表示される
     *
     * @return void
     */
    public function test_password_is_required()
    {
        // employeeユーザーの登録ページを開く
        // $response = $this->get($this->route);
        $this->visitRegisterPage();

        // ステータスコード 200 を確認
        // $response->assertStatus(200);

        // 登録時にパスワードを空にする
        // $response = $this->post($this->route, [
        $response = $this->followingRedirects()->post($this->route, [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);
        // セッションのエラーメッセージを確認
        // $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);

        // 実際の画面上にエラーメッセージが表示されているかを確認
        $response->assertSeeText('パスワードを入力してください');
    }

    /**
     * 正常に登録できることを確認する
     *
     * @return void
     */
    public function test_user_can_register_successfully()
    {
        // employeeユーザーを登録する
        $response = $this->post($this->route, [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        // employeesテーブルに登録されたかを確認
        // $response->assertRedirect(route('email.authentication.invitation', ['employeeId' => Employee::first()->id]));
        $this->assertDatabaseHas('employees', ['email' => 'test@example.com']);
    }
}
