<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceRequestRequest;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\AttendanceCorrectionBreak;
use App\Models\AttendanceStatus;
use App\Models\BreakModel;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * ==============================
     * 従業員ユーザーの勤怠登録関連
     * ==============================
     */

    /**
     * 従業員の勤怠登録画面を表示
     *
     * @route GET /employee/attendance-create
     * @return \Illuminate\View\View
     */
    public function attendanceCreate()
    {
        $employee = auth('employee')->user();

        $today = Carbon::today()->toDateString();

        // 今日の勤怠レコードを取得
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', $today)
            ->with('status')
            ->first();

        //AttendanceStatusモデルでステータスを定数化。attendance_statusesテーブルから「勤務外」のレコードを取得
        $status = AttendanceStatus::where('status', AttendanceStatus::STATUS_OFF)->first();

        // 「出勤」は1日に1回だけ押下できるため、今日の勤怠レコードがあり、かつ 勤怠ステータスが退勤済みか判定
        if ($todayAttendance && ($todayAttendance->attendance_status_id === $status->id)) {

            // エラーメッセージ表示
            return to_route('employee.attendance.message')->with(['message' => '本日はすでに出勤登録と退勤登録が完了しています。']);
        }

        // 最新の勤怠レコードを取得（日をまたいで退勤する場合のため、当日制限なし）
        $attendance = Attendance::where('employee_id', $employee->id)->latest()->with('status')->first();

        // 勤怠登録画面を表示
        return view('attendance.employee.attendance-create', compact('attendance'));
    }

    /**
     * 従業員の勤怠登録（メッセージ）画面を表示
     *
     * @route GET /employee/attendance-message
     * @return \Illuminate\View\View
     */
    public function attendanceMessage()
    {
        return view('attendance.employee.attendance-message');
    }

    /**
     * 従業員の出勤登録処理
     *
     * @route POST /employee//attendance/clock-in
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clockIn()
    {
        $employee = auth('employee')->user();

        $today = Carbon::today()->toDateString();

        //AttendanceStatusモデルでステータスを定数化。attendance_statusesテーブルから「勤務中」のレコードを取得
        $status = AttendanceStatus::where('status', AttendanceStatus::STATUS_ON)->first();

        // 出勤テーブルにレコード作成（「勤務中」ステータスを付与）
        Attendance::create([
            'employee_id' => $employee->id,
            'date' => $today,
            'start_time' => Carbon::now()->toDateTimeString(), // 日付と時間を含める
            'attendance_status_id' => $status->id,
        ]);

        return redirect()->route('employee.attendance.create');
    }

    /**
     * 従業員の休憩開始登録処理
     *
     * @route POST /employee//attendance/break-start
     * @return \Illuminate\Http\RedirectResponse
     */
    public function breakStart()
    {
        $employee = auth('employee')->user();

        $attendance = Attendance::where('employee_id', $employee->id)->latest()->first();

        // 休憩テーブルにレコード作成
        $attendance->breaks()->create([
            'break_start_time' => Carbon::now()->toDateTimeString(), // 日付と時間を含める
        ]);

        // AttendanceStatusモデルでステータスを定数化。attendance_statusesテーブルから「休憩中」のレコードを取得
        $status = AttendanceStatus::where('status', AttendanceStatus::STATUS_BREAK)->first();

        // 勤怠テーブルのステータスを「休憩中」に更新
        $attendance->update([
            'attendance_status_id' => $status->id
        ]);

        return redirect()->route('employee.attendance.create');
    }

    /**
     * 従業員の休憩終了登録処理
     * @route POST /employee//attendance/break-end
     * @return \Illuminate\Http\RedirectResponse
     */
    public function breakEnd()
    {
        $employee = auth('employee')->user();

        $attendance = Attendance::where('employee_id', $employee->id)->latest()->first();

        // 休憩テーブルから、休憩戻りの登録がされていないレコードを取得し、休憩戻り時間を登録
        $lastBreak = $attendance->breaks()->whereNull('break_end_time')->latest()->first();

        $lastBreak->update(['break_end_time' => Carbon::now()->toDateTimeString()]); // 日付と時間を含める

        // AttendanceStatusモデルでステータスを定数化。attendance_statusesテーブルから「勤務中」のレコードを取得
        $status = AttendanceStatus::where('status', AttendanceStatus::STATUS_ON)->first();

        // 勤怠テーブルのステータスを「勤務中」に更新
        $attendance->update([
            'attendance_status_id' => $status->id
        ]);

        return redirect()->route('employee.attendance.create');
    }

    /**
     * 従業員の退勤登録処理
     * @route POST /employee//attendance/break-out
     * @return \Illuminate\Http\RedirectResponse
     */
    public function clockOut()
    {
        $employee = auth('employee')->user();

        $attendance = Attendance::where('employee_id', $employee->id)->latest()->first();

        // AttendanceStatusモデルでステータスを定数化。attendance_statusesテーブルから「勤務外」のレコードを取得
        $status = AttendanceStatus::where('status', AttendanceStatus::STATUS_OFF)->first();

        // 勤怠テーブルのステータスを「勤務外」に更新
        $attendance->update([
            'end_time' => Carbon::now()->toDateTimeString(), // 日付と時間を含める
            'attendance_status_id'  => $status->id,
        ]);

        return to_route('employee.attendance.message')->with(['message' => 'お疲れ様でした。']);
    }

    /**
     * 従業員の勤怠一覧画面を表示
     *
     * @route GET /employee/attendance-list
     * @return \Illuminate\View\View
     */
    public function attendanceList(Request $request)
    {
        // ログイン中の従業員情報を取得
        $employee = auth('employee')->user();

        // 指定された月を取得（デフォルトは現在の月）
        $month = $request->query('month', now()->format('Y-m'));

        // 指定月の勤怠データを取得
        $attendances = Attendance::where('employee_id', $employee->id)
            ->where('date', 'like', $month . '%')
            ->orderBy('date', 'asc')
            ->with('breaks') // 休憩データも取得
            ->get();

        // 勤務時間・休憩時間を計算
        $this->calculateBreakTime($attendances);
        $this->calculateWorkTime($attendances);

        return view('attendance.employee.attendance-list', compact('attendances', 'month'));
    }

    /**
     * 従業員の勤怠詳細画面を表示
     *
     * @route GET /employee/attendance-show
     * @return \Illuminate\View\View
     */
    public function attendanceShow($attendanceId)
    {
        // ログイン中の従業員情報を取得
        $employee = auth('employee')->user();

        // リクエストされたattendance_idの勤怠情報を取得
        $attendance = Attendance::with('breaks')
            ->findOrFail($attendanceId);

        return view('attendance.employee.attendance-show', compact('attendance'));
    }

    /**
     * ==============================
     * 管理者ユーザーの勤怠管理関連
     * ==============================
     */

    /**
     * 日次勤怠一覧画面（管理者）を表示
     *
     * @route GET /admin/attendance/daily-list
     * @return \Illuminate\View\View
     */
    public function attendanceDailyList(Request $request, $date = null)
    {
        // 日付を取得 (指定がない場合は今日の日付)
        $date = $date ? Carbon::parse($date) : Carbon::today();

        // 特定の日に勤怠情報のあるの従業員全員の勤怠データを取得
        $attendances = Attendance::where('date', $date->toDateString())
            ->with(['employee', 'breaks'])
            ->get();

        // 勤務時間・休憩時間を計算
        $this->calculateBreakTime($attendances);
        $this->calculateWorkTime($attendances);

        return view('attendance.admin.attendance-daily-list', compact('attendances', 'date'));
    }

    /**
     * 勤怠詳細画面（管理者）を表示
     *
     * @route GET /admin/attendance/{attendanceId}/show
     * @return \Illuminate\View\View
     */
    public function adminAttendanceShow($attendanceId)
    {
        // リクエストされたattendance_idの勤怠情報を取得
        $attendance = Attendance::with(['employee', 'breaks'])->findOrFail($attendanceId);

        return view('attendance.admin.attendance-show', compact('attendance'));
    }

    /**
     * 勤怠情報の更新処理（管理者）
     *
     * @route POST /admin/attendance/{attendanceId}/correct
     * @return \Illuminate\Http\RedirectResponse
     */
    public function adminAttendanceCorrect(AttendanceRequestRequest $request, $attendanceId)
    {
        // リクエストされたattendance_idの勤怠情報を取得
        $attendance = Attendance::with('breaks')->findOrFail($attendanceId);

        // 修正前のデータを保存
        $originalStartTime = $attendance->start_time;
        $originalEndTime = $attendance->end_time;

        // 勤怠修正履歴を保存
        $attendanceCorrection = AttendanceCorrection::create([
            'attendance_id' => $attendance->id,
            'admin_id' => Auth::id(), // 現在ログインしている管理者
            'original_start_time' => $originalStartTime,
            'original_end_time' => $originalEndTime,
            'corrected_start_time' => \Carbon\Carbon::parse($attendance->date . ' ' . $request->start_time),
            'corrected_end_time' => \Carbon\Carbon::parse($attendance->date . ' ' . $request->end_time),
            'reason' => $request->input('reason'),
        ]);

        // 出勤時間・退勤時間の更新
        $attendance->update([
            'start_time' => \Carbon\Carbon::parse($attendance->date . ' ' . $request->start_time), // 日付と組み合わせる
            'end_time' => \Carbon\Carbon::parse($attendance->date . ' ' . $request->end_time), // 日付と組み合わせる
        ]);

        // リクエストされた break データの処理
        $requestBreaks = $request->input('breaks', []);

        foreach ($requestBreaks as $breakId => $breakData) {
            if (!empty($breakData['start']) && !empty($breakData['end'])) {

                // 修正前のデータを保存
                $originalBreakStartTime = BreakModel::findOrFail($breakId)->break_start_time;
                $originalBreakEndTime = BreakModel::findOrFail($breakId)->break_end_time;

                // 休憩時間の修正履歴を保存
                AttendanceCorrectionBreak::create([
                    'attendance_correction_id' => $attendanceCorrection->id,
                    'break_id' => $breakId,
                    'original_break_start' => $originalBreakStartTime,
                    'original_break_end' => $originalBreakEndTime,
                    'corrected_break_start' => \Carbon\Carbon::parse($attendance->date . ' ' . $breakData['start']),
                    'corrected_break_end' => \Carbon\Carbon::parse($attendance->date . ' ' . $breakData['end']),
                ]);

                // 既存の break レコードを更新
                BreakModel::where('id', $breakId)->update([
                    'break_start_time' => \Carbon\Carbon::parse($attendance->date . ' ' . $breakData['start']), // 日付と組み合わせる
                    'break_end_time' => \Carbon\Carbon::parse($attendance->date . ' ' . $breakData['end']), // 日付と組み合わせる
                ]);
            } elseif (empty($breakData['start']) && empty($breakData['end'])) {
                // start と end の両方がない場合、NULL に更新
                BreakModel::where('id', $breakId)->update([
                    'break_start_time' => null,
                    'break_end_time' => null,
                ]);
            }
        }

        return redirect()->route('admin.attendance.show', $attendanceId)->with('success', $attendance->employee->name . 'さんの勤怠情報を修正しました。');
    }

    /**
     * 従業員一覧画面（管理者）を表示
     *
     * @route GET /admin/attendance/employee-list
     * @return \Illuminate\View\View
     */
    public function attendanceEmployeeList()
    {
        // 全従業員のデータを取得
        $employees = Employee::with('attendances')
            ->get();

        return view('attendance.admin.employee-list', compact('employees'));
    }

    /**
     * 従業員別月次勤怠一覧画面（管理者）を表示
     *
     * @route GET /admin/attendance/monthly-list/{employeeId}
     * @return \Illuminate\View\View
     */
    public function attendanceMonthlyList(Request $request, $employeeId)
    {
        // リクエストされた従業員情報を取得
        $employee = Employee::findOrFail($employeeId);

        // 指定された月を取得（デフォルトは現在の月）
        $month = $request->query('month', now()->format('Y-m'));

        // 指定月の勤怠データを取得
        $attendances = Attendance::where('employee_id', $employeeId)
            ->where('date', 'like', $month . '%')
            ->orderBy('date', 'asc')
            ->with(['employee', 'breaks']) // 従業員データ、休憩データも取得
            ->get();

        // 勤務時間・休憩時間を計算
        $this->calculateBreakTime($attendances);
        $this->calculateWorkTime($attendances);

        return view('attendance.admin.attendance-monthly-list', compact('employee', 'attendances', 'month'));
    }

    /**
     * ==============================
     * 共通メソッド
     * ==============================
     */

    /**
     * 休憩時間を計算し、フォーマットするメソッド
     */
    private function calculateBreakTime($attendances)
    {
        foreach ($attendances as $attendance) {
            $totalBreakMinutes = $attendance->breaks->sum(function ($break) {
                if ($break->break_start_time && $break->break_end_time) {
                    return Carbon::parse($break->break_start_time)->diffInMinutes(Carbon::parse($break->break_end_time));
                }
                return 0;
            });
            // 休憩時間を H:i 形式でフォーマット
            $attendance->total_break_time = $totalBreakMinutes > 0
                ? sprintf('%d:%02d', floor($totalBreakMinutes / 60), $totalBreakMinutes % 60)
                : '-';
        }
    }

    /**
     * 勤務時間を計算し、フォーマットするメソッド
     */
    private function calculateWorkTime($attendances)
    {
        foreach ($attendances as $attendance) {
            if ($attendance->start_time && $attendance->end_time) {
                $totalBreakMinutes = $attendance->breaks->sum(function ($break) {
                    if ($break->break_start_time && $break->break_end_time) {
                        return Carbon::parse($break->break_start_time)->diffInMinutes(Carbon::parse($break->break_end_time));
                    }
                    return 0;
                });
                $workMinutes = Carbon::parse($attendance->start_time)->diffInMinutes(Carbon::parse($attendance->end_time)) - $totalBreakMinutes;
            } else {
                $workMinutes = 0;
            }
            // 勤務時間を H:i 形式でフォーマット
            $attendance->total_work_time = $workMinutes > 0
                ? sprintf('%d:%02d', floor($workMinutes / 60), $workMinutes % 60)
                : '-';
        }
    }
}
