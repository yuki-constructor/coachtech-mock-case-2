<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Admin;
use App\Models\AttendanceStatus;
use App\Models\AttendanceRequest;
use App\Models\AttendanceRequestStatus;
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
 *  ID:11 勤怠詳細情報修正機能（一般ユーザー）
 *  ===================================================
 */
class AttendanceRequestTest extends TestCase
{
    // データベーストランザクションを利用
    use DatabaseTransactions;

    // ルートをプロパティとして定義
    protected string $attendanceRequestRoute;
    protected string $pendingRequestListRoute;
    protected string $adminPendingRequestListRoute;
    protected string $adminRequestShowRoute;
    protected string $approvedRequestListRoute;

    // 作成した従業員を保持するプロパティ
    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();

        // 共通ルートの設定(勤怠詳細画面、勤怠修正申請ルート、修正申請承認画面は動的に生成するため、後で生成（パラメータ付き）)
        $this->pendingRequestListRoute = route('employee.attendance.request.list.pending');
        $this->adminPendingRequestListRoute = route('admin.attendance.request.list.pending');
        // $this->adminRequestShowRoute = route('admin.attendance.request.show');
        $this->approvedRequestListRoute  = route('employee.attendance.request.list.approved');

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
     * Attendance レコードと関連するbreaksレコードを作成する共通メソッド
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
        // $date = Carbon::parse('2025-03-15');

        // 「勤務外」のステータスIDを取得
        // $statusOff = AttendanceStatus::where('status', AttendanceStatus::STATUS_OFF)->first();

        // $attendance = Attendance::create([
        //     'employee_id'          => $this->employee->id,
        //     'attendance_status_id' => $statusOff->id,
        //     'date'                 => $date->toDateString(),
        //     'start_time'           => $date->copy()->setTimeFromTimeString($start)->toDateTimeString(),
        //     'end_time'             => $date->copy()->setTimeFromTimeString($end)->toDateTimeString(),
        // ]);

        // 出勤時刻（09:00）と退勤時刻（18:00）を設定
        // $startTime = $date->copy()->setTime(9, 0, 0)->toDateTimeString();
        // $endTime   = $date->copy()->setTime(18, 0, 0)->toDateTimeString();

        // $attendance = Attendance::create([
        //     'employee_id'         => $this->employee->id,
        //     'attendance_status_id' => $statusOff->id,
        //     'date'                => $date->toDateString(),
        //     'start_time'          => $startTime,
        //     'end_time'            => $endTime,
        // ]);

        //   // 休憩情報を登録（BreakModel を利用）
        //   BreakModel::create([
        //     'attendance_id'    => $attendance->id,
        //     'break_start_time' => $date->copy()->setTimeFromTimeString($breakStart)->toDateTimeString(),
        //     'break_end_time'   => $date->copy()->setTimeFromTimeString($breakEnd)->toDateTimeString(),
        // ]);

        // 休憩開始時刻（12:00）と休憩終了時刻（13:00）を設定
        // $breakStart = $date->copy()->setTime(12, 0, 0)->toDateTimeString();
        // $breakEnd   = $date->copy()->setTime(13, 0, 0)->toDateTimeString();

        // $break = BreakModel::create([
        //     'attendance_id'     => $attendance->id,
        //     'break_start_time'  => $breakStart,
        //     'break_end_time'    =>  $breakEnd,
        // ]);

        // 2025-03-15 ～ 2025-03-19 の期間を作成
        $period = CarbonPeriod::create('2025-03-15', '2025-03-19');
        $attendances = collect();

        // 「勤務外」のステータスIDを取得
        $statusOff = AttendanceStatus::where('status', AttendanceStatus::STATUS_OFF)->first();

        foreach ($period as $date) {
            // 出勤時刻（09:00）と退勤時刻（18:00）の設定
            $startTime = $date->copy()->setTime(9, 0, 0)->toDateTimeString();
            $endTime   = $date->copy()->setTime(18, 0, 0)->toDateTimeString();

            $attendance = Attendance::create([
                'employee_id'          => $this->employee->id,
                'attendance_status_id' => $statusOff->id,
                'date'                 => $date->toDateString(),
                'start_time'           => $startTime,
                'end_time'             => $endTime,
            ]);

            // 休憩情報（12:00～13:00）を登録
            $breakStart = $date->copy()->setTime(12, 0, 0)->toDateTimeString();
            $breakEnd   = $date->copy()->setTime(13, 0, 0)->toDateTimeString();

            BreakModel::create([
                'attendance_id'     => $attendance->id,
                'break_start_time'  => $breakStart,
                'break_end_time'    => $breakEnd,
            ]);

            $attendances->push($attendance);
        }

        // return $attendance;
        return $attendances;
    }

    /**
     *
     * 出勤時間が退勤時間より後になっている場合、エラーメッセージが表示されることを確認
     *
     * @return void
     */
    public function test_invalid_clock_times_shows_error_message()
    {
        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // ダミーの勤怠データ作成
        $this->createAttendanceRecord();

        $attendance = Attendance::where('employee_id', $this->employee->id)->first();

        // 勤怠詳細画面へアクセス
        $detailUrl = route('employee.attendance.show', ['attendanceId' => $attendance->id]);
        $response = $this->get($detailUrl);
        $response->assertStatus(200);

        // 出勤時間を退勤時間より後に設定（出勤 18:00, 退勤 09:00）
        $data = [
            'start_time' => '18:00',
            'end_time'   => '09:00',
            'breaks'     => [
                $attendance->breaks()->first()->id => [
                    // 'break_start_time' => '12:00',
                    'start' => '12:00',
                    // 'break_end_time'   => '12:30',
                    'end'   => '12:30',
                ],
            ],
            'reason' => '修正申請テスト'
        ];

        // 勤怠修正申請ルート
        $requestUrl = route('employee.attendance.request', ['attendanceId' => $attendance->id]);

        //  勤怠修正申請処理
        // $response = $this->post($route, $data);
        $response = $this->followingRedirects()->post($requestUrl, $data);
        // dd($response);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }

    /**
     *
     * 休憩開始時間が退勤時間より後の場合にエラーメッセージが表示されることを確認
     *
     * @return void
     */
    public function test_invalid_break_start_time_shows_error_message()
    {
        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // ダミーの勤怠データ作成
        $this->createAttendanceRecord();

        $attendance = Attendance::where('employee_id', $this->employee->id)->first();

        // 勤怠詳細画面へアクセス
        $detailUrl = route('employee.attendance.show', ['attendanceId' => $attendance->id]);
        $response = $this->get($detailUrl);
        $response->assertStatus(200);

        // 休憩開始時間を退勤時間より後に設定（退勤 18:00, 休憩開始 19:00, 終了 19:30）
        $data = [
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'breaks'     => [
                $attendance->breaks()->first()->id => [
                    'start' => '19:00',
                    'end'   => '19:30',
                ],
            ],
            'reason' => '修正申請テスト'
        ];


        // 勤怠修正申請ルート
        $requestUrl = route('employee.attendance.request', ['attendanceId' => $attendance->id]);

        //  勤怠修正申請処理
        // $response = $this->post($route, $data);
        $response = $this->followingRedirects()->post($requestUrl, $data);
        // dd($response);
        $response->assertSee('休憩時間が勤務時間外です');
    }

    /**
     *
     * 休憩開始時間が退勤時間より後の場合にエラーメッセージが表示されることを確認
     *
     * @return void
     */
    public function test_invalid_break_end_time_shows_error_message()
    {
        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // ダミーの勤怠データ作成
        $this->createAttendanceRecord();

        $attendance = Attendance::where('employee_id', $this->employee->id)->first();

        // 勤怠詳細画面へアクセス
        $detailUrl = route('employee.attendance.show', ['attendanceId' => $attendance->id]);
        $response = $this->get($detailUrl);
        $response->assertStatus(200);

        // 休憩終了時間を退勤時間より後に設定（退勤 18:00, 休憩開始 19:00, 終了 19:30）
        $data = [
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'breaks'     => [
                $attendance->breaks()->first()->id => [
                    'start' => '12:00',
                    'end'   => '19:30',
                ],
            ],
            'reason' => '修正申請テスト'
        ];


        // 勤怠修正申請ルート
        $requestUrl = route('employee.attendance.request', ['attendanceId' => $attendance->id]);

        //  勤怠修正申請処理
        // $response = $this->post($route, $data);
        $response = $this->followingRedirects()->post($requestUrl, $data);
        // dd($response);
        $response->assertSee('休憩時間が勤務時間外です');
    }

    /**
     *
     * 備考欄が未入力の場合に「備考を記入してください」というエラーメッセージが表示されることを確認
     */
    public function test_empty_reason_shows_error_message()
    {
        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // ダミーの勤怠データ作成
        $this->createAttendanceRecord();

        $attendance = Attendance::where('employee_id', $this->employee->id)->first();

        // 勤怠詳細画面へアクセス
        $detailUrl = route('employee.attendance.show', ['attendanceId' => $attendance->id]);
        $response = $this->get($detailUrl);
        $response->assertStatus(200);

        // 勤務時刻、休憩時刻は正常に設定、備考は空欄に設定
        $data = [
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'breaks'     => [
                $attendance->breaks()->first()->id => [
                    'start' => '12:00',
                    'end'   => '12:30',
                ],
            ],
            'reason' => ''
        ];

        // 勤怠修正申請ルート
        $requestUrl = route('employee.attendance.request', ['attendanceId' => $attendance->id]);

        //  勤怠修正申請処理
        // $response = $this->post($route, $data);
        $response = $this->followingRedirects()->post($requestUrl, $data);
        // dd($response);
        $response->assertSee('備考を記入してください');
    }

    /**
     *
     * 修正申請処理が実行され、管理者の承認画面と申請一覧画面（承認待ち）に反映されることを確認
     *
     * @return void
     */
    public function test_valid_attendance_request_is_executed()
    {
        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // ダミーの勤怠データ作成assertSee
        $this->createAttendanceRecord();

        $attendance = Attendance::where('employee_id', $this->employee->id)->first();

        // 正しい修正申請
        $data = [
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'breaks'     => [
                $attendance->breaks()->first()->id => [
                    'start' => '12:00',
                    'end'   => '12:30',
                ],
            ],
            'reason' => '正しい修正申請'
        ];

        // 勤怠修正申請ルート
        $requestUrl = route('employee.attendance.request', ['attendanceId' => $attendance->id]);

        //  勤怠修正申請処理
        // $response = $this->post($route, $data);
        $response = $this->post($requestUrl, $data);

        //  すでに登録済みのadminユーザーにてログイン
        $admin = Admin::first();
        $this->actingAs($admin, 'admin');

        // 勤怠修正申請一覧画面（承認待ち）（管理者用）にアクセス
        $response = $this->get($this->adminPendingRequestListRoute);
        // dd($response);

        //  勤怠修正申請一覧画面（承認待ち）（管理者用）にタイトルやテスト用employeeユーザーの名前が表示されていることを確認
        $response->assertSee('申請一覧');
        $response->assertSee('承認待ち');
        // dd($this->employee->name);
        $response->assertSee($this->employee->name);

        // 勤怠修正申請承認画面（管理者用）にアクセス
        $attendanceRequest = AttendanceRequest::where('attendance_id', $attendance->id)->first();
        // dd($attendanceRequest);
        $requestShowUrl = route('admin.attendance.request.show', ['attendanceRequestId' => $attendanceRequest->id]);
        $response = $this->get($requestShowUrl);
        // dd($response);

        // 勤怠修正申請承認画面（管理者用）にタイトルやテスト用employeeユーザーの名前、テスト内で行った勤怠修正の時刻（休憩終了 12：30）が表示されていることを確認
        $response->assertSee('勤怠詳細');
        $response->assertSee($this->employee->name);
        // dd($this->employee->name);
        $response->assertSee('12:30');
    }

    /**
     *
     * 申請一覧画面（承認待ち）にログインユーザーが行った申請が全て表示されていることを確認
     *
     * @return void
     */
    public function test_pending_request_list_displays_employee_requests()
    {
        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // ダミーの勤怠データ作成（2025-03-15～2025-03-19）
        $attendances = $this->createAttendanceRecord();

        // 各勤怠レコードごとに修正申請を送信
        foreach ($attendances as $attendance) {
            $data = [
                'start_time' => '09:00',
                'end_time'   => '18:00',
                'breaks'     => [
                    $attendance->breaks()->first()->id => [
                        'start' => '12:00',
                        'end'   => '12:30',
                    ],
                ],
                'reason' => '申請一覧テスト'
            ];
            $requestUrl = route('employee.attendance.request', ['attendanceId' => $attendance->id]);
            $this->post($requestUrl, $data);
        }

        // 管理者ユーザーでログイン
        $admin = Admin::first();
        $this->actingAs($admin, 'admin');

        // 勤怠修正申請一覧画面（承認待ち）（管理者用）にアクセス
        $response = $this->get($this->adminPendingRequestListRoute);
        $response->assertStatus(200);
        $response->assertSee('申請一覧');
        $response->assertSee('承認待ち');
        $response->assertSee($this->employee->name);

        // ログインユーザーが行った申請（2025-03-15～2025-03-19）が全て表示されているか確認
        foreach ($attendances as $attendance) {
            $expectedDate = Carbon::parse($attendance->date)->format('Y/m/d');
            $response->assertSee($expectedDate);
        }
    }

    /**
     *
     * 申請一覧画面（承認済み）に、管理者が承認した修正申請が全て表示されていることを確認
     *
     * @return void
     */
    public function test_approved_request_list_displays_admin_approved_requests()
    {
        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // ダミーの勤怠データ作成（2025-03-15～2025-03-19）
        $attendances = $this->createAttendanceRecord();

        // 各勤怠レコードごとに修正申請を送信
        foreach ($attendances as $attendance) {
            $data = [
                'start_time' => '09:00',
                'end_time'   => '18:00',
                'breaks'     => [
                    $attendance->breaks()->first()->id => [
                        'start' => '12:00',
                        'end'   => '12:30',
                    ],
                ],
                'reason' => '申請一覧テスト'
            ];
            $requestUrl = route('employee.attendance.request', ['attendanceId' => $attendance->id]);
            $this->post($requestUrl, $data);
        }

        // 管理者ユーザーでログイン
        $admin = Admin::first();
        $this->actingAs($admin, 'admin');

        // 修正申請レコードのステータスを「承認済み」に更新
        $pendingStatusId = AttendanceRequestStatus::where('request_status', AttendanceRequestStatus::STATUS_PENDING_APPROVAL)->first()->id;
        // dd($pendingStatusId);
        $approvedStatusId = AttendanceRequestStatus::where('request_status', AttendanceRequestStatus::STATUS_APPROVED)->first()->id;
        // $attendanceRequest = AttendanceRequest::where('attendance_id', $attendance->id)
        //     ->where('attendance_request_status_id', $pendingStatusId)
        //     ->first();
        // $attendanceRequest->update(['attendance_request_status_id' => $approvedStatusId]);

        // $attendanceRequests = AttendanceRequest::where('attendance_id', $this->employee->attendances()->pluck('id'))
        //     ->where('attendance_request_status_id', $pendingStatusId)
        //     ->get();
        $attendanceRequests = AttendanceRequest::whereIn('attendance_id', $this->employee->attendances()->pluck('id')->toArray())
            ->where('attendance_request_status_id', $pendingStatusId)
            ->get();

        // 偶数番目のリクエストを承認済みに変更
        foreach ($attendanceRequests as $index => $request) {
            if (($index + 1) % 2 === 0) { // 偶数番目
                $request->update(['attendance_request_status_id' => $approvedStatusId]);
            }
        }

        // 承認済み申請一覧画面にアクセス
        $approvedUrl = route('employee.attendance.request.list.approved');
        $response = $this->get($approvedUrl);
        $response->assertStatus(200);
        $response->assertSee('承認済み');
        $response->assertSee($this->employee->name);

        // 承認済みに更新した各修正申請（偶数番目のリクエスト）が表示されていることを検証
        foreach ($attendanceRequests as $index => $request) {
            if (($index + 1) % 2 === 0) {
                $expectedDate = Carbon::parse($request->attendance->date)->format('Y/m/d');
                // dd($expectedDate);
                $response->assertSee($expectedDate);
            }
        }
        // dd($expectedDate);
        // dd($response);
    }

    /**
     *
     * 申請一覧画面の「詳細」リンクを押下すると申請詳細画面に遷移することを確認
     *
     * @return void
     */
    public function test_request_detail_navigation()
    {
        // テスト用従業員でログイン
        $employee = $this->loginEmployee();

        // ダミーの勤怠データ作成
        $this->createAttendanceRecord();

        $attendance = Attendance::where('employee_id', $this->employee->id)->first();

        // 修正申請を送信
        $requestUrl = route('employee.attendance.request', ['attendanceId' => $attendance->id]);
        $data = [
            'start_time' => '09:00',
            'end_time'   => '18:00',
            'breaks'     => [
                $attendance->breaks()->first()->id => [
                    'start' => '12:00',
                    'end'   => '12:30',
                ],
            ],
            'reason' => '詳細リンクテスト'
        ];
        $this->post($requestUrl, $data);

        $attendanceRequest = AttendanceRequest::where('attendance_id', $attendance->id)
            ->where('reason', '詳細リンクテスト')
            ->first();

        // 修正申請詳細画面にアクセス
        $detailUrl = route('employee.attendance.request.show', ['attendanceRequestId' => $attendanceRequest->id]);
        $response = $this->get($detailUrl);
        $response->assertStatus(200);
        
        // 修正申請詳細画面にタイトルやテスト用employeeユーザーの名前、テスト内で行った勤怠修正の時刻（休憩終了 12：30）が表示されていることを確認
        $response->assertSee('勤怠詳細');
        $response->assertSee($this->employee->name);
        // dd($this->employee->name);
        $response->assertSee('12:30');
        $response->assertSee('＊承認待ちのため修正はできません。');
        // dd($response);
    }
}
