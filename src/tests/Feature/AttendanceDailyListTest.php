<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Admin;
use App\Models\AttendanceStatus;
use App\Models\BreakModel;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * ===================================================
 *  （テスト項目）
 *  ID:12 勤怠一覧情報取得機能（管理者）
 * ===================================================
 */
class AttendanceDailyListTest extends TestCase
{
    // データベーストランザクションを利用
    use DatabaseTransactions;

    // ルートをプロパティとして定義
    protected string $adminDailyListRoute;

    // 作成した従業員を保持するプロパティ
    protected $employees;

    protected function setUp(): void
    {
        parent::setUp();

        // 複数の従業員を作成（3件）
        $this->employees = Employee::factory()->count(3)->create();

        // すべての従業員に対して、特定の日（2025-03-15）の勤怠レコードと休憩レコードを作成
        $this->createDailyAttendanceRecords('2025-03-15');
    }

    /**
     * 指定日付の Attendance レコードと関連するbreaksレコードを、すべての従業員に対して作成する共通メソッド
     *
     * @param string
     */
    protected function createDailyAttendanceRecords(string $dateStr)
    {
        $date = Carbon::parse($dateStr);
        $statusOff = AttendanceStatus::where('status', AttendanceStatus::STATUS_OFF)->first();

        foreach ($this->employees as $employee) {
            $attendance = Attendance::create([
                'employee_id'          => $employee->id,
                'attendance_status_id' => $statusOff->id,
                'date'                 => $date->toDateString(),
                'start_time'           => $date->copy()->setTime(9, 0, 0)->toDateTimeString(),
                'end_time'             => $date->copy()->setTime(18, 0, 0)->toDateTimeString(),
            ]);

            BreakModel::create([
                'attendance_id'    => $attendance->id,
                'break_start_time' => $date->copy()->setTime(12, 0, 0)->toDateTimeString(),
                'break_end_time'   => $date->copy()->setTime(13, 0, 0)->toDateTimeString(),
            ]);
        }
    }

    /**
     *
     * 日次勤怠一覧画面（管理者用）で、その日になされた全ての employeeユーザーの勤怠情報が正確に確認できることを確認
     *
     * @return void
     */
    public function test_daily_attendance_list_displays_all_employee_attendances()
    {
        //  すでに登録済みのadminユーザーにてログイン
        $admin = Admin::first();
        $this->actingAs($admin, 'admin');

        // 日次勤怠一覧画面（管理者用）にアクセス
        $date = Carbon::parse('2025-03-15');
        $url = route('admin.attendance.daily-list', ['date' => $date->toDateString()]);
        $response = $this->get($url);
        $response->assertStatus(200);

        // タイトルに日付が表示される（例："2025年3月15日の勤怠"）
        $expectedTitle = $date->isoFormat('YYYY年M月D日') . 'の勤怠';
        $response->assertSee($expectedTitle);

        // 各従業員の名前や出退勤時刻、休憩時間の合計が表示されるか確認
        foreach ($this->employees as $employee) {
            $response->assertSee($employee->name);
            $response->assertSee('09:00');
            $response->assertSee('18:00');
            // 休憩時間は calculateBreakTime() により計算され、"1:00" の形で表示される（1時間）
            $response->assertSee('1:00');
        }
    }

    /**
     *
     * 日次勤怠一覧画面（管理者用）に遷移した際に現在の日付が表示される
     *
     * @return void
     */
    public function test_daily_attendance_list_displays_current_date()
    {
        //  すでに登録済みのadminユーザーにてログイン
        $admin = Admin::first();
        $this->actingAs($admin, 'admin');

        // 日付パラメータを指定しない場合は、コントローラーで今日の日付（Carbon::today()）が使われる想定
        // テスト用に today を固定（2025-03-15）
        Carbon::setTestNow('2025-03-15');
        $url = route('admin.attendance.daily-list');
        $response = $this->get($url);
        $response->assertStatus(200);
        $expectedTitle = Carbon::today()->isoFormat('YYYY年M月D日') . 'の勤怠';
        $response->assertSee($expectedTitle);

        // 各従業員の名前や出退勤時刻、休憩時間の合計が表示されるか確認
        foreach ($this->employees as $employee) {
            $response->assertSee($employee->name);
            $response->assertSee('09:00');
            $response->assertSee('18:00');
            // 休憩時間は calculateBreakTime() により計算され、"1:00" の形で表示される（1時間）
            $response->assertSee('1:00');
        }
        
        Carbon::setTestNow();
    }

    /**
     *
     * 日次勤怠一覧画面（管理者用）の「前日」を押下した時に前の日の勤怠情報が表示される
     *
     * @return void
     */
    public function test_previous_day_navigation()
    {
        //  すでに登録済みのadminユーザーにてログイン
        $admin = Admin::first();
        $this->actingAs($admin, 'admin');

        $currentDate = Carbon::parse('2025-03-15');
        $prevDate = $currentDate->copy()->subDay();
        $url = route('admin.attendance.daily-list', ['date' => $prevDate->toDateString()]);
        $response = $this->get($url);
        $response->assertStatus(200);

        // タイトルに日付が表示される（例："2025年3月14日の勤怠"）
        $expectedTitle = $prevDate->isoFormat('YYYY年M月D日') . 'の勤怠';
        $response->assertSee($expectedTitle);
    }

    /**
     *
     * 日次勤怠一覧画面（管理者用）の「翌日」を押下した時に翌日の日付の勤怠情報が表示される
     */
    public function test_next_day_navigation()
    {
        //  すでに登録済みのadminユーザーにてログイン
        $admin = Admin::first();
        $this->actingAs($admin, 'admin');

        $currentDate = Carbon::parse('2025-03-15');
        $nextDate = $currentDate->copy()->addDay();
        $url = route('admin.attendance.daily-list', ['date' => $nextDate->toDateString()]);
        $response = $this->get($url);
        $response->assertStatus(200);

        // タイトルに日付が表示される（例："2025年3月16日の勤怠"）
        $expectedTitle = $nextDate->isoFormat('YYYY年M月D日') . 'の勤怠';
        $response->assertSee($expectedTitle);
    }
}
