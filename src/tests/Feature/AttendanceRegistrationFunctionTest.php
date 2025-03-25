<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\AttendanceStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * ===================================================
 *  （テスト項目）
 *  ID:6 出勤機能
 * ===================================================
 */
class AttendanceRegistrationFunctionTest extends TestCase
{
    // データベーストランザクションを利用
    use DatabaseTransactions;

    // ルートをプロパティとして定義
    protected string $attendanceCreateRoute;
    protected string $attendanceMessageRoute;
    protected string $attendanceClockInRoute;
    protected string $attendanceListRoute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->attendanceCreateRoute = route('employee.attendance.create');
        $this->attendanceMessageRoute = route('employee.attendance.message');
        $this->attendanceClockInRoute = route('attendance.clock-in');
        $this->attendanceListRoute = route('employee.attendance.list');
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
     * 勤怠登録画面の出勤ボタンが正しく機能する
     *
     * @return void
     */
    public function test_clock_in_button_functionality()
    {
        // テスト用従業員を作成してログイン（勤務外＝未出勤の場合）
        $employee = $this->loginEmployee();

        // 勤怠登録画面をGETし、「出勤」ボタンが表示されていることを確認
        $response = $this->get($this->attendanceCreateRoute);
        $response->assertStatus(200);
        $response->assertSeeText('出勤');

        // 出勤処理を実施
        // $response = $this->post($this->attendanceClockInRoute);

        // $response->assertRedirect($this->attendanceCreateRoute);
        // $response = $this->followingRedirects()->post($this->attendanceCreateRoute);
        $response = $this->followingRedirects()->post($this->attendanceClockInRoute);

        // 出勤処理後、画面上に「出勤中」と表示されることを確認
        // $response = $this->get($this->attendanceCreateRoute);
        $response->assertStatus(200);
        $response->assertSeeText('出勤中');
    }

    /**
     * 出勤は一日一回のみできる
     *
     * @return void
     */
    public function test_clock_in_only_once_per_day()
    {
        // テスト用従業員を作成してログイン
        $employee = $this->loginEmployee();

        // 「勤務外」の勤怠ステータスを取得
        $statusOff = AttendanceStatus::where('status', AttendanceStatus::STATUS_OFF)->first();

        // 当日の勤怠レコードを作成（既に退勤済み＝出退勤完了の状態）
        Attendance::create([
            'employee_id'         => $employee->id,
            'date'                => Carbon::today()->toDateString(),
            'start_time'          => Carbon::now()->toDateTimeString(),
            'end_time'            => Carbon::now()->toDateTimeString(),
            'attendance_status_id' => $statusOff->id,
        ]);

        // 勤怠登録画面へアクセス
        $response = $this->get($this->attendanceCreateRoute);
        // 既に出退勤済みの場合、出勤ボタンが表示されず、メッセージ画面(勤務ボタンが表示されない)へリダイレクトすることを確認
        $response->assertRedirect($this->attendanceMessageRoute);
    }

    /**
     * 出勤時刻が管理画面で確認できる
     *
     * @return void
     */
    public function test_clock_in_time_is_recorded_in_attendance_list_screen()
    {
        // テスト用従業員を作成してログイン
        $employee = $this->loginEmployee();

        // 出勤処理を実施
        $response = $this->post($this->attendanceClockInRoute);

        // 管理画面（勤怠一覧）にアクセス
        $response = $this->get($this->attendanceListRoute);
        $response->assertStatus(200);

        // DB上の出勤時刻を取得し、フォーマット（H:i形式）
        // $attendance = Attendance::where('employee_id', $employee->id)->first();
        // $attendance = Attendance::where('employee_id', $employee->id)->latest()->first();
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', Carbon::today()->toDateString())
            ->first();
        $formattedTime = Carbon::parse($attendance->start_time)->format('H:i');
        // dd($formattedTime);

        // 管理画面に正しい出勤時刻が表示されていることを確認
        $response->assertSeeText($formattedTime);
    }
}
