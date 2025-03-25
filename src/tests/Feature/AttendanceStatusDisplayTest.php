<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AttendanceStatusDisplayTest extends TestCase
{
    // データベーストランザクションを利用
    use DatabaseTransactions;

    // ルートをプロパティとして定義
    protected string $attendanceCreateRoute;
    protected string $attendanceMessageRoute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->attendanceCreateRoute = route('employee.attendance.create');
        $this->attendanceMessageRoute = route('employee.attendance.message');
        // // 必要に応じて、言語設定を日本語にする
        // app()->setLocale('ja');
    }

    /**
     * employeeユーザーを登録、ログイン状態にする共通メソッド
     *
     * @return \App\Models\Attendance
     */
    protected function loginEmployee(): Employee
    {
        // テスト用従業員を作成
        $employee = Employee::factory()->create();

        // ログイン状態にする
        $this->actingAs($employee, 'employee');

        // // 今日の日付を取得
        // $today = Carbon::today()->toDateString();

        // // 指定のステータスを取得
        // $status = AttendanceStatus::where('status', $statusValue)->first();

        // // 勤怠レコードを作成
        // $attendance = Attendance::create([
        //     'employee_id'         => $employee->id,
        //     'date'                => $today,
        //     'start_time'          => now(),  // 任意
        //     'end_time'            => now()->addHours(8), // 任意
        //     'attendance_status_id' => $status->id,
        // ]);

        return $employee;
    }

    // /**
    //  * 指定の勤怠ステータスを持つ出勤レコードを作成する
    //  *
    //  * @param \App\Models\Employee $employee
    //  * @param string $statusName   '勤務中'、'休憩中'、'退勤済'など
    //  * @return \App\Models\Attendance
    //  */
    // protected function createAttendanceRecord(Employee $employee, string $statusName): Attendance
    // {
    //     // 本来は事前にシード済みである前提（例: STATUS_ON, STATUS_BREAK, STATUS_OFF, STATUS退勤済）
    //     $status = AttendanceStatus::where('status', $statusName)->first();
    //     return Attendance::create([
    //         'employee_id' => $employee->id,
    //         'date' => Carbon::today()->toDateString(),
    //         // ここでは開始時間は適当に現在時刻、退勤時間は null（未登録）とする例です
    //         'start_time' => Carbon::now()->toDateTimeString(),
    //         'end_time' => null,
    //         'attendance_status_id' => $status->id,
    //     ]);
    // }

    /**
     * 勤務外の場合、画面上に「勤務外」と表示されることを確認
     * （勤怠レコードがない状態）
     *
     * @return void
     */
    public function test_status_off_is_displayed()
    {
        $employee = $this->loginEmployee();

        // 勤怠レコードを作成しない → 初期状態は「勤務外」と表示される
        $response = $this->get($this->attendanceCreateRoute);
        $response->assertStatus(200);
        $response->assertSeeText('勤務外');
    }

    // /**
    //  * 勤怠ステータスが「勤務外」の場合、画面上に「勤務外」と表示されるか確認する
    //  *
    //  * @return void
    //  */
    // public function test_status_off_is_displayed()
    // {
    //     // 「勤務外」ステータス
    //     $attendance = $this->createAttendanceWithStatus(AttendanceStatus::STATUS_OFF);

    //     $response = $this->get($this->attendanceCreateRoute);
    //     $response->assertStatus(200);
    //     $response->assertSeeText('勤務外');
    // }

    /**
     * 出勤中の場合、画面上に「出勤中」と表示されることを確認
     */
    public function test_status_on_is_displayed()
    {
        $employee = $this->loginEmployee();

        // AttendanceStatusモデルでステータスを定数化。attendance_statusesテーブルから「勤務中」のレコードを取得
        $status = AttendanceStatus::where('status', AttendanceStatus::STATUS_ON)->first();

        // 出勤テーブルにレコード作成（勤務中ステータスを付与）
        Attendance::create([
            'employee_id' => $employee->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->toDateTimeString(),
            'attendance_status_id' => $status->id,
        ]);

        $response = $this->get($this->attendanceCreateRoute);
        $response->assertStatus(200);
        $response->assertSeeText('出勤中');
    }

    // /**
    //  * 勤怠ステータスが「勤務中」の場合、画面上に「出勤中」と表示されるか確認する
    //  *
    //  * @return void
    //  */
    // public function test_status_on_is_displayed()
    // {
    //     $attendance = $this->createAttendanceWithStatus(AttendanceStatus::STATUS_ON);

    //     $response = $this->get($this->attendanceCreateRoute);
    //     $response->assertStatus(200);
    //     $response->assertSeeText('出勤中');
    // }

    /**
     * 休憩中の場合、画面上に「休憩中」と表示されることを確認
     */
    public function test_status_break_is_displayed()
    {
        $employee = $this->loginEmployee();

        // AttendanceStatusモデルでステータスを定数化。attendance_statusesテーブルから「休憩中」のレコードを取得
        $status = AttendanceStatus::where('status', AttendanceStatus::STATUS_BREAK)->first();

        // 出勤テーブルにレコード作成（休憩中ステータスを付与）
        Attendance::create([
            'employee_id' => $employee->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->toDateTimeString(),
            'attendance_status_id' => $status->id,
        ]);


        $response = $this->get($this->attendanceCreateRoute);
        $response->assertStatus(200);
        $response->assertSeeText('休憩中');
    }

    // /**
    //  * 勤怠ステータスが「休憩中」の場合、画面上に「休憩中」と表示されるか確認する
    //  *
    //  * @return void
    //  */
    // public function test_status_break_is_displayed()
    // {
    //     $attendance = $this->createAttendanceWithStatus(AttendanceStatus::STATUS_BREAK);

    //     $response = $this->get($this->attendanceCreateRoute);
    //     $response->assertStatus(200);
    //     $response->assertSeeText('休憩中');
    // }

    /**
     * 退勤済の場合、画面上に「退勤済」と表示されることを確認
     */
    public function test_status_checked_out_is_displayed()
    {
        $employee = $this->loginEmployee();

        // AttendanceStatusモデルでステータスを定数化。attendance_statusesテーブルから「勤務外」のレコードを取得
        $status = AttendanceStatus::where('status', AttendanceStatus::STATUS_OFF)->first();

        // 出勤テーブルにレコード作成（勤務外ステータスを付与）
        Attendance::create([
            'employee_id' => $employee->id,
            'date' => Carbon::today()->toDateString(),
            'start_time' => Carbon::now()->toDateTimeString(),
            'attendance_status_id' => $status->id,
        ]);

        // $response = $this->get($this->attendanceCreateRoute);
        // $response->$this->get($this->attendanceMessageRoute);

        $response = $this->followingRedirects()->get($this->attendanceCreateRoute);

        // dd($response);
        // dd($response->status());

        // $response->assertRedirect($this->attendanceMessageRoute);

        $response->assertStatus(200);
        $response->assertSeeText('退勤済');

        // $response = $this->get($this->attendanceCreateRoute);
        // $response->assertRedirect($this->attendanceMessageRoute);

        // // 次に、リダイレクト先のページにアクセス
        // $response = $this->get($this->attendanceMessageRoute);
        // $response->assertStatus(200);
        // $response->assertSeeText('退勤済');
    }

    // /**
    //  * 勤怠ステータスが「退勤済」の場合、画面上に「退勤済」と表示されるか確認する
    //  *
    //  * @return void
    //  */
    // public function test_status_checked_out_is_displayed()
    // {
    //     // テスト用に「勤務外」を指定
    //     $attendance = $this->createAttendanceWithStatus(AttendanceStatus::STATUS_OFF);

    //     $response = $this->get($this->attendanceCreateRoute);
    //     $response->assertStatus(200);
    //     $response->assertSeeText('退勤済');
    // }
}
