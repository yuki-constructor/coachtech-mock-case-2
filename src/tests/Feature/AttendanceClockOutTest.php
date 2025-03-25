<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * ===================================================
 *  （テスト項目）
 *  ID:8 退勤機能
 * ===================================================
 */
class AttendanceClockOutTest extends TestCase
{
    // データベーストランザクションを利用
    use DatabaseTransactions;

    protected string $attendanceCreateRoute;
    protected string $attendanceClockInRoute;
    protected string $attendanceClockOutRoute;
    protected string $attendanceMessageRoute;
    // attendanceShowRoute はテスト内で動的に生成するので setUp() では固定しない

    protected function setUp(): void
    {
        parent::setUp();

        // ルートをプロパティとして定義
        $this->attendanceCreateRoute   = route('employee.attendance.create');
        $this->attendanceClockInRoute  = route('attendance.clock-in');
        $this->attendanceClockOutRoute = route('attendance.clock-out');
        $this->attendanceMessageRoute  = route('employee.attendance.message');
    }

    /**
     * employeeユーザーを登録、ログイン状態にする共通メソッド
     *
     * @return \App\Models\Attendance
     */
    protected function loginEmployee(?Employee $employee = null): Employee
    {
        // テスト用従業員を作成
        if (!$employee) {
            $employee = Employee::factory()->create();
        }
        // ログイン状態にする
        $this->actingAs($employee, 'employee');

        return $employee;
    }


    /**
     * 出勤処理を共通化（POSTで出勤処理を実施）
     */
    protected function clockIn(Employee $employee): void
    {
        $this->post($this->attendanceClockInRoute);
    }

    /**
     *
     * 勤怠登録画面で退勤ボタンが正しく機能する
     *
     * @return void
     */
    public function test_clock_out_button_functionality()
    {
        // テスト用従業員を作成してログイン
        $employee = $this->loginEmployee();

        // 出勤処理で状態を「勤務中」にする
        $this->clockIn($employee);

        // 勤怠登録画面にアクセスし、「退勤」ボタンが表示されていることを確認
        $response = $this->get($this->attendanceCreateRoute);
        $response->assertStatus(200);
        $response->assertSee('退勤');

        // POSTで退勤処理を実施（退勤後は attendanceMessageRoute へリダイレクト）
        $response = $this->followingRedirects()->post($this->attendanceClockOutRoute);
        // dd($response);
        $response->assertStatus(200);

        // コントローラーで、退勤処理後、attendanceMessageRoute へリダイレクトされるように設定している。退勤処理後、attendanceMessageRoute へリダイレクトされたかを、「お疲れ様でした。」のメッセージを返しているかで検証
        $response->assertSee('お疲れ様でした。');

        // 画面上に「退勤済」と表示されることを確認
        // $response = $this->get($this->attendanceCreateRoute);
        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }

    /**
     *
     * 退勤時刻が管理画面で確認できる
     *
     * @return void
     */
    public function test_clock_out_time_is_recorded_in_management_screen()
    {
        // テスト用従業員を作成してログイン
        $employee = $this->loginEmployee();

        // 出勤処理を実施し「勤務中」にする
        $this->clockIn($employee);

        // 退勤処理を実施し、「勤務外」（退勤済）にする
        $this->followingRedirects()->post($this->attendanceClockOutRoute);

        // DB上で最新の勤怠レコードを取得
        $attendance = Attendance::where('employee_id', $employee->id)
            ->latest()
            ->first();

        // 動的に勤怠詳細画面（管理画面）のURLを生成する
        $attendanceShowUrl = route('employee.attendance.show', ['attendanceId' => $attendance->id]);

        // 管理画面（勤怠詳細画面）にアクセス
        $response = $this->get($attendanceShowUrl);
        $response->assertStatus(200);

        // DB上の退勤時刻をフォーマット（例：H:i形式）
        $formattedClockOutTime = Carbon::parse($attendance->end_time)->format('H:i');

        // 管理画面（勤怠詳細画面）上に、退勤時刻が正しく表示されていることを確認
        // $response->assertSeeText($formattedClockOutTime);
        $response->assertSee($formattedClockOutTime);
    }
}
