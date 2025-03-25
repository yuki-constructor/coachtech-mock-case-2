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
 *  ID:9 勤怠一覧情報取得機能（一般ユーザー）
 * ===================================================
 */
class AttendanceListTest extends TestCase
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
        $this->attendanceMessageRoute = route('employee.attendance.message');
        $this->attendanceClockInRoute = route('attendance.clock-in');
        $this->attendanceListRoute = route('employee.attendance.list');

        // 従業員ユーザーを1件作成し、プロパティに保持
        $this->employee = Employee::factory()->create();

        // ダミーの勤怠情報を、2025-02-01 から 2025-04-30 まで作成する
        $period = CarbonPeriod::create('2025-02-01', '2025-04-30');

        // 「勤務外」のステータスIDを取得
        $statusOff = AttendanceStatus::where('status', AttendanceStatus::STATUS_OFF)->first();

        // '2025-02-01'～ '2025-04-30'の期間の勤怠データを登録
        foreach ($period as $date) {

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

            // 休憩開始時刻（12:00）と休憩終了時刻（13:00）を設定
            $breakStart = $date->copy()->setTime(12, 0, 0)->toDateTimeString();
            $breakEnd   = $date->copy()->setTime(13, 0, 0)->toDateTimeString();

            $break = BreakModel::create([
                'attendance_id'     => $attendance->id,
                'break_start_time'  => $breakStart,
                'break_end_time'    =>  $breakEnd,
            ]);
        }
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
     *
     * 勤怠一覧画面に、自分が行った勤怠情報が全て表示されていることを確認
     *
     * @return void
     */
    public function test_all_attendances_for_logged_in_employee_are_displayed()
    {
        // テスト時刻をダミーの勤怠登録期間内（2025-02-01～ 2025-04-30）に設定
        Carbon::setTestNow(Carbon::parse('2025-03-15'));

        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // 現在の月を対象に勤怠一覧画面へアクセス
        $month = Carbon::now()->format('Y-m');
        $url = route('employee.attendance.list', ['month' => $month]);
        $response = $this->get($url);
        $response->assertStatus(200);

        // ダミーの勤怠情報は、対象月に1日～最終日まで全日登録されている
        // 例として、03月01日の出勤情報と、03月31日の勤怠情報が表示されるか検証

        // 日付(2025-03-01)
        $expectedDate = Carbon::parse('2025-03-01')->locale('ja')->isoFormat('MM/DD (ddd)');
        // 出勤時刻
        $expectedStartTime = Carbon::parse('09:00:00
')->locale('ja')->format('H:i');
        // dd($expectedStartTime);
        // 退勤時刻
        $expectedEndTime = Carbon::parse('18:00:00')->locale('ja')->format('H:i');
        // dd($expectedEndTime);
        // 休憩時間の合計
        $expectedTotalBreakTime = Carbon::parse('01:00')->locale('ja')->format('G:i');
        // dd($expectedTotalBreakTime);

        $response->assertSee($expectedDate);
        $response->assertSee($expectedStartTime);
        $response->assertSee($expectedEndTime);
        $response->assertSee($expectedTotalBreakTime);


        // 日付(2025-03-31)
        $expectedDate = Carbon::parse('2025-03-31')->locale('ja')->isoFormat('MM/DD (ddd)');
        // 出勤時刻
        $expectedStartTime = Carbon::parse('09:00:00
')->locale('ja')->format('H:i');
        // dd($expectedStartTime);
        // 退勤時刻
        $expectedEndTime = Carbon::parse('18:00:00')->locale('ja')->format('H:i');
        // dd($expectedEndTime);
        // 休憩時間の合計
        $expectedTotalBreakTime = Carbon::parse('01:00')->locale('ja')->format('G:i');
        // dd($expectedTotalBreakTime);

        $response->assertSee($expectedDate);
        $response->assertSee($expectedStartTime);
        $response->assertSee($expectedEndTime);
        $response->assertSee($expectedTotalBreakTime);

        Carbon::setTestNow(); // テスト時刻のリセット
    }

    /**
     *
     * 勤怠一覧画面に遷移した際に現在の月が表示されることを確認
     *
     * @return void
     */
    public function test_current_month_is_displayed_on_attendance_list()
    {
        // テスト時刻をダミーの勤怠登録期間内（2025-02-01～ 2025-04-30）に設定
        Carbon::setTestNow(Carbon::parse('2025-03-15'));

        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // 現在の月を対象に勤怠一覧画面へアクセス
        $month = Carbon::now()->format('Y-m');
        $url = route('employee.attendance.list', ['month' => $month]);
        $response = $this->get($url);
        $response->assertStatus(200);

        // 勤怠一覧画面では、<span class="month">{{ Carbon::parse($month)->format('Y/m') }}</span> と表示される
        $expectedMonth = Carbon::now()->format('Y/m');  // "2025/03"
        $response->assertSee($expectedMonth);

        Carbon::setTestNow();
    }

    /**
     *
     * 「勤怠一覧画面の『前月』を押下した時に表示月の前月の情報が表示される」
     *
     * @return void
     */
    public function test_previous_month_button_displays_previous_month_attendances()
    {
        // テスト時刻をダミーの勤怠登録期間内（2025-02-01～ 2025-04-30）に設定
        Carbon::setTestNow(Carbon::parse('2025-03-15'));

        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        //attendance-list.blade.php では、前月のリンクは次のように設定されている。 <a href="{{ route('employee.attendance.list', ['month' => \Carbon\Carbon::parse($month)->subMonth()->format('Y-m')]) }}">&larr;前月</a>
        // 2025-03の場合の前月は 2025-02
        $prevMonth = Carbon::now()->subMonth()->format('Y-m'); // "2025-02"
        $prevUrl = route('employee.attendance.list', ['month' => $prevMonth]);
        $prevResponse = $this->get($prevUrl);
        // dd($prevResponse);
        $prevResponse->assertStatus(200);

        // 2025-02-01 の勤怠情報が表示されているか確認
        $expectedDate = Carbon::parse('2025-02-01')->locale('ja')->isoFormat('MM/DD (ddd)');
        $prevResponse->assertSee($expectedDate);

        Carbon::setTestNow();
    }

    /**
     *
     * 「勤怠一覧画面の『翌月』を押下した時に表示月の翌月の情報が表示される」
     *
     * @return void
     */
    public function test_next_month_button_displays_next_month_attendances()
    {

        // テスト時刻をダミーの勤怠登録期間内（2025-02-01～ 2025-04-30）に設定
        Carbon::setTestNow(Carbon::parse('2025-03-15'));

        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        //attendance-list.blade.php では、翌月のリンクは次のように設定されている。 <a href="{{ route('employee.attendance.list', ['month' => \Carbon\Carbon::parse($month)->addMonth()->format('Y-m')]) }}">&larr;翌月</a>
        // 2025-03 の翌月は 2025-04
        $nextMonth = Carbon::now()->addMonth()->format('Y-m'); // "2025-04"
        $nextUrl = route('employee.attendance.list', ['month' => $nextMonth]);
        $nextResponse = $this->get($nextUrl);
        $nextResponse->assertStatus(200);

        // 例として、2025-04-01 の勤怠情報が表示されているか検証
        $expectedDate = Carbon::parse('2025-04-01')->locale('ja')->isoFormat('MM/DD (ddd)');
        $nextResponse->assertSee($expectedDate);

        Carbon::setTestNow();
    }

    /**
     *
     * 「勤怠一覧画面の『詳細』を押下すると、その日の勤怠詳細画面に遷移する」
     *
     * @return void
     */
    public function test_detail_button_redirects_to_attendance_detail_page()
    {
        // テスト時刻をダミーの勤怠登録期間内（2025-02-01～ 2025-04-30）に設定
        Carbon::setTestNow(Carbon::parse('2025-03-15'));

        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // 対象月 2025-03 の勤怠一覧画面にアクセス
        $month = Carbon::now()->format('Y-m');  // "2025-03"
        $listUrl = route('employee.attendance.list', ['month' => $month]);
        $response = $this->get($listUrl);
        $response->assertStatus(200);

        // ダミーの勤怠情報のうち、最初の1件を取得
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', 'like', "$month%")
            ->first();

        // 勤怠詳細画面のURLを、attendanceId をパラメータとして動的に生成
        $attendanceShowUrl = route('employee.attendance.show', ['attendanceId' => $attendance->id]);

        // 詳細画面にアクセス
        $detailResponse = $this->get($attendanceShowUrl);
        $detailResponse->assertStatus(200);

        // 詳細画面には「勤怠詳細」タイトルや、該当日の勤怠情報が表示されていることを確認
        // $formattedDate = Carbon::parse($attendance->date)->locale('ja')->format('Y年');
        $formattedDate = Carbon::parse($attendance->date)->format('Y年');
        // $formattedDate = Carbon::parse($attendance->date)->locale('ja')->format('n月j日');
        $formattedDate = Carbon::parse($attendance->date)->format('n月j日');
        $detailResponse->assertSee($formattedDate);

        Carbon::setTestNow();
    }
}
