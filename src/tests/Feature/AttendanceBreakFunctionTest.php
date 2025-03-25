<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\BreakModel;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * ===================================================
 *  （テスト項目）
 *  ID:7 休憩機能
 * ===================================================
 */
class AttendanceBreakFunctionTest extends TestCase
{
    // データベーストランザクションを利用
    use DatabaseTransactions;

    protected string $attendanceCreateRoute;
    protected string $attendanceClockInRoute;
    protected string $attendanceBreakStartRoute;
    protected string $attendanceBreakEndRoute;
    protected string $attendanceListRoute;
    // protected string $attendanceShowRoute;

    protected function setUp(): void
    {
        parent::setUp();

        // ルートをプロパティとして定義
        $this->attendanceCreateRoute   = route('employee.attendance.create');
        $this->attendanceClockInRoute  = route('attendance.clock-in');
        $this->attendanceBreakStartRoute = route('attendance.break-start');
        $this->attendanceBreakEndRoute   = route('attendance.break-end');
        $this->attendanceListRoute     = route('employee.attendance.list');
        // $this->attendanceShowRoute     = route('employee.attendance.show');
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
     * 勤怠登録画面の休憩ボタンが正しく機能する
     *
     * @return void
     */
    public function test_break_start_button_functionality()
    {
        // テスト用従業員を作成してログイン
        $employee = $this->loginEmployee();

        // 出勤処理で「勤務中」にする
        $this->clockIn($employee);

        // 勤怠登録画面で「休憩入」ボタンが表示されていることを確認
        $response = $this->get($this->attendanceCreateRoute);
        $response->assertStatus(200);
        $response->assertSeeText('休憩入');

        // POSTで休憩開始処理を実施（followingRedirects() でリダイレクト先の画面を取得）
        $response = $this->followingRedirects()->post($this->attendanceBreakStartRoute);
        // dd($response);
        $response->assertStatus(200);
        // 勤怠登録画面に表示されている勤怠ステータスが「休憩中」となっていることを確認
        $response->assertSeeText('休憩中');
    }

    /**
     *
     * 休憩は一日に何回でもできる
     *
     * @return void
     */
    public function test_multiple_breaks_allowed()
    {
        // テスト用従業員を作成してログイン
        $employee = $this->loginEmployee();

        // 出勤処理で「勤務中」にする
        $this->clockIn($employee);

        // 1回目の休憩サイクル：休憩開始処理
        $this->post($this->attendanceBreakStartRoute);

        // 1回目の休憩サイクル：休憩終了処理（リダイレクト先の画面を取得）
        $response = $this->followingRedirects()->post($this->attendanceBreakEndRoute);

        // $attendance = Attendance::where('employee_id', $employee->id)->latest()->first();
        // $response = BreakModel::where('attendance_id', $attendance->id)->latest()->first();
        // dd($response);

        // 休憩終了後、勤怠登録画面には再び「休憩入」ボタンが表示されることを確認（休憩は複数回可能）
        // $response = $this->get($this->attendanceCreateRoute);
        $response->assertStatus(200);
        $response->assertSeeText('休憩入');
    }

    /**
     *
     * 勤怠登録画面の休憩戻ボタンが正しく機能する
     *
     * @return void
     */
    public function test_break_end_button_functionality()
    {
        // テスト用従業員を作成してログイン
        $employee = $this->loginEmployee();

        // 出勤処理で「勤務中」にする
        $this->clockIn($employee);

        // 休憩開始処理
        $this->post($this->attendanceBreakStartRoute);

        //勤怠登録画面で「休憩戻」ボタンが表示されているか確認
        $response = $this->get($this->attendanceCreateRoute);
        $response->assertStatus(200);
        $response->assertSeeText('休憩戻');

        // 休憩終了処理（followingRedirects() でリダイレクト先を取得）
        $response = $this->followingRedirects()->post($this->attendanceBreakEndRoute);
        $response->assertStatus(200);

        // 休憩終了後は「勤務中」に戻るので、「休憩入」ボタンが表示される
        $response->assertSeeText('出勤中');
        $response->assertSeeText('休憩入');
    }

    /**
     *
     * 休憩戻は一日に何回でもできる
     *
     * @return void
     */
    public function test_multiple_break_end_allowed()
    {
        // テスト用従業員を作成してログイン
        $employee = $this->loginEmployee();

        // 出勤処理で「勤務中」にする
        $this->clockIn($employee);

        // 1回目の休憩サイクル：休憩開始→休憩終了
        $this->post($this->attendanceBreakStartRoute);
        $this->post($this->attendanceBreakEndRoute);

        // ２回目の休憩サイクル：休憩開始処理（リダイレクト先の画面を取得）
        $response = $this->followingRedirects()->post($this->attendanceBreakStartRoute);

        // 休憩開始後、勤怠登録画面上に「休憩戻」ボタンが表示されることを確認
        $response->assertStatus(200);
        $response->assertSeeText('休憩戻');
    }

    /**
     *
     * 休憩時刻が管理画面（勤怠一覧）で確認できる
     *
     * @return void
     */
    public function test_break_time_is_recorded_in_attendance_list_screen()
    {
        // テスト用従業員を作成してログイン
        $employee = $this->loginEmployee();

        // 出勤処理で「勤務中」にする
        $this->clockIn($employee);

        // 休憩開始→休憩終了の処理を実施
        $this->post($this->attendanceBreakStartRoute);
        $this->post($this->attendanceBreakEndRoute);

        // // 管理画面（勤怠詳細画面）にアクセス
        // $response = $this->get($this->attendanceShowRoute);
        // $response->assertStatus(200);

        // DB上で最新の勤怠レコードを取得（休憩情報もリレーションで取得）
        $attendance = Attendance::where('employee_id', $employee->id)
            ->latest()
            ->with('breaks')
            ->first();

        // dd($attendance);

        $attendanceShowUrl = route('employee.attendance.show', ['attendanceId' => $attendance->id]);

        // 管理画面（勤怠詳細画面）にアクセス
        $response = $this->get($attendanceShowUrl);
        $response->assertStatus(200);


        //    $response->assertSeeText($attendance->date);
        // DB上の休憩した日時をフォーマット
        $formattedYear = Carbon::parse($attendance->date)->format('Y年');
        // dd($formattedYear);
        $formattedDate = Carbon::parse($attendance->date)->format('n月j日');
        // dd($formattedDate);
        // $response->assertSeeText($attendance->breaks->first()->break_start_time);
        $formattedStartTime=  Carbon::parse($attendance->breaks->first()->break_start_time)->format('H:i');
        // dd($formattedStartTime);
        $formattedEndTime=  Carbon::parse($attendance->breaks->first()->break_end_time)->format('H:i');
        // dd($formattedEndTime);


        // 管理画面（勤怠詳細画面）で、休憩日時が正確に表示されていることを確認
        $response->assertSee($formattedYear);
        $response->assertSee($formattedDate);
        $response->assertSee($formattedStartTime);
        $response->assertSee($formattedEndTime);
    }
}
