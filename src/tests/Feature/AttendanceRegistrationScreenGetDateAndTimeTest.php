<?php

namespace Tests\Feature;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * ===================================================
 *  （テスト項目）
 *  ID:4 日時取得機能
 * ===================================================
 */
class AttendanceRegistrationScreenGetDateAndTimeTest extends TestCase
{
    // データベーストランザクションを利用
    use DatabaseTransactions;

    // private function loginEmployee()
    // {
    //     // テスト用の従業員を作成
    //     $employee = Employee::factory()->create();

    //     // 従業員として認証
    //     Auth::guard('employee')->login($employee);

    //     return $employee;
    // }

    /**
     * 勤怠打刻画面で現在の日時が正しく表示される
     *
     * @return void
     */
    #[Test]
    public function test_current_date_and_time_are_displayed_correctly()
    {
        // テスト用の従業員を作成
        $employee = Employee::factory()->create();

        // テストユーザーをログイン
        //   Auth::guard('employee')->login($employee);
        $this->actingAs($employee, 'employee');

        // 勤怠打刻画面へアクセス
        $response = $this->get(route('employee.attendance.create'));

        // dd($response->getContent());

        // ステータスコード 200 を確認
        $response->assertStatus(200);

        // Carbonのロケールを日本語に設定
        Carbon::setLocale('ja');

        // 現在の日時を取得
        $now = Carbon::now();

         // 日本語の曜日リスト
         $weekdays = ['日', '月', '火', '水', '木', '金', '土'];

         // 日本語の曜日を取得
         $weekday = $weekdays[$now->dayOfWeek];

         // フォーマットを設定
        //  $formattedDate = $now->format('Y年n月j日 (D)');
         $formattedDate = $now->format("Y年n月j日") . " ({$weekday})";

        $formattedTime = $now->format('H:i');

        // ページ内に正しい日付情報が含まれているかチェック
        $response->assertSeeText($formattedDate);
        $response->assertSeeText($formattedTime);
    }
}
