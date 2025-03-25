<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\BreakModel;
use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * ===================================================
 *  （テスト項目）
 *  ID:10 勤怠一覧情報取得機能（一般ユーザー）
 * ===================================================
 */
class AttendanceDetailTest extends TestCase
{
    // データベーストランザクションを利用
    use DatabaseTransactions;

    // ルートをプロパティとして定義
    protected string $attendanceCreateRoute;
    protected string $attendanceMessageRoute;
    protected string $attendanceClockInRoute;
    protected string $attendanceListRoute;

    // 作成した従業員を保持するプロパティ
    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();

        // 共通ルートの設定
        $this->attendanceCreateRoute = route('employee.attendance.create');

        // テスト用従業員を1件作成し、プロパティに保持
        $this->employee = Employee::factory()->create();
    }

    /**
     * employeeユーザーをログイン状態にする共通メソッド
     *
     * @return \App\Models\Employee
     */
    protected function loginEmployee()
    {
        // setUp() で作成された $this->employee を使ってログイン状態にする
        $this->actingAs($this->employee, 'employee');

        return $this->employee;
    }

    /**
     * Attendance レコード（および関連する休憩レコード）を作成する共通メソッド
     *
     * @return \App\Models\Attendance
     */
    protected function createAttendanceRecord(
        // string $dateStr = '2025-03-15',
        // string $start = '09:00',
        // string $end = '18:00',
        // string $breakStart = '12:00',
        // string $breakEnd = '12:30'
    )
    {
        // $date = Carbon::parse($dateStr);
        $date = Carbon::parse('2025-03-15');

        // 「勤務外」のステータスIDを取得
        $statusOff = AttendanceStatus::where('status', AttendanceStatus::STATUS_OFF)->first();

        // $attendance = Attendance::create([
        //     'employee_id'          => $this->employee->id,
        //     'attendance_status_id' => $statusOff->id,
        //     'date'                 => $date->toDateString(),
        //     'start_time'           => $date->copy()->setTimeFromTimeString($start)->toDateTimeString(),
        //     'end_time'             => $date->copy()->setTimeFromTimeString($end)->toDateTimeString(),
        // ]);

        // 出勤時刻（09:00）と退勤時刻（18:00）を設定
        $startTime = $date->copy()->setTime(9, 0, 0)->toDateTimeString();
        $endTime   = $date->copy()->setTime(18, 0, 0)->toDateTimeString();

        $attendance = Attendance::create([
            'employee_id'         => $this->employee->id,
            'attendance_status_id' => $statusOff->id,
            'date'                => $date->toDateString(),
            'start_time'          => $startTime,
            'end_time'            => $endTime,
        ]);

        //   // 休憩情報を登録（BreakModel を利用）
        //   BreakModel::create([
        //     'attendance_id'    => $attendance->id,
        //     'break_start_time' => $date->copy()->setTimeFromTimeString($breakStart)->toDateTimeString(),
        //     'break_end_time'   => $date->copy()->setTimeFromTimeString($breakEnd)->toDateTimeString(),
        // ]);

        // 休憩開始時刻（12:00）と休憩終了時刻（13:00）を設定
        $breakStart = $date->copy()->setTime(12, 0, 0)->toDateTimeString();
        $breakEnd   = $date->copy()->setTime(13, 0, 0)->toDateTimeString();

        $break = BreakModel::create([
            'attendance_id'     => $attendance->id,
            'break_start_time'  => $breakStart,
            'break_end_time'    =>  $breakEnd,
        ]);

        return $attendance;
    }

    /**
     *
     * 勤怠詳細画面の「名前」がログインユーザーの氏名になっていることを確認
     *
     * @return void
     */
    public function test_attendance_detail_name_is_correct()
    {
        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // ダミーの勤怠データ作成（2025-03-15）
        $attendance = $this->createAttendanceRecord();

        // 勤怠詳細画面へアクセス
        $detailUrl = route('employee.attendance.show', ['attendanceId' => $attendance->id]);
        $response = $this->get($detailUrl);
        $response->assertStatus(200);

        // 勤怠詳細画面にて、ログインユーザーの氏名が表示されていることを確認
        $response->assertSee($employee->name);
    }

    /**
     *
     * 勤怠詳細画面の「日付」が選択した日付になっていることを確認
     *
     * @return void
     */
    public function test_attendance_detail_date_is_correct()
    {
        // テスト用従業員でログイン
        $this->loginEmployee();

        // ダミーの勤怠データ作成（2025-03-15）
        // $attendance = $this->createAttendanceRecord('2025-03-15');
        $attendance = $this->createAttendanceRecord();

        // 勤怠詳細画面のURLを、attendanceId をパラメータとして動的に生成
        $attendanceShowUrl = route('employee.attendance.show', ['attendanceId' => $attendance->id]);

        // 詳細画面にアクセス
        $response = $this->get($attendanceShowUrl);
        $response->assertStatus(200);

        // 詳細画面に、勤怠情報の該当日が表示されていることを確認
        $expectedYear = Carbon::parse($attendance->date)->format('Y年');  // "2025年"
        $expectedDay = Carbon::parse($attendance->date)->format('n月j日');  // "3月15日"
        $response->assertSee($expectedYear);
        $response->assertSee($expectedDay);
    }

    /**
     *
     * 「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致していることを確認
     *
     * @return void
     */
    public function test_attendance_detail_clock_times_are_correct()
    {
        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // $attendance = $this->createAttendanceRecord('2025-03-15', '09:00', '18:00');

        // ダミーの勤怠データ作成（'2025-03-15', '09:00', '18:00'）
        $attendance = $this->createAttendanceRecord();

        // 勤怠詳細画面のURLを、attendanceId をパラメータとして動的に生成
        $attendanceShowUrl = route('employee.attendance.show', ['attendanceId' => $attendance->id]);

        // 詳細画面にアクセス
        $response = $this->get($attendanceShowUrl);
        $response->assertStatus(200);

        // 出勤・退勤時刻（'09:00', '18:00'）が表示されていることを確認
        $expectedStart = Carbon::parse($attendance->start_time)->format('H:i'); // "09:00"
        $expectedEnd = Carbon::parse($attendance->end_time)->format('H:i');     // "18:00"
        $response->assertSee($expectedStart);
        $response->assertSee($expectedEnd);
    }

    /**
     *
     * 「休憩」にて記されている時間がログインユーザーの打刻と一致していることを確認
     *
     * @return void
     */
    public function test_attendance_detail_break_times_are_correct()
    {
        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // // ダミーの勤怠データ作成
        // $attendance = $this->createAttendanceRecord('2025-03-15', '09:00', '18:00', '12:00', '12:30');

        // ダミーの勤怠データ作成（'2025-03-15', '09:00', '18:00', '12:00', '12:30'）
        $attendance = $this->createAttendanceRecord();


        // 勤怠詳細画面のURLを、attendanceId をパラメータとして動的に生成
        $attendanceShowUrl = route('employee.attendance.show', ['attendanceId' => $attendance->id]);

        // 詳細画面にアクセス
        $response = $this->get($attendanceShowUrl);
        $response->assertStatus(200);

        // 休憩情報は foreach でループして表示されるので、ここでは最初のレコードを検証
        $break = $attendance->breaks()->first();
        $expectedBreakStart = Carbon::parse($break->break_start_time)->format('H:i'); // "12:00"
        $expectedBreakEnd = Carbon::parse($break->break_end_time)->format('H:i');     // "12:30"
        $response->assertSee($expectedBreakStart);
        $response->assertSee($expectedBreakEnd);
    }
}
