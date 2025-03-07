<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * ==============================
 * 従業員ユーザーの勤怠登録関連
 * ==============================
 */
class AttendanceController extends Controller
{
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

        foreach ($attendances as $attendance) {

            // 休憩時間の合計を計算（分単位）
            $totalBreakMinutes = $attendance->breaks->sum(function ($break) {
                if ($break->break_end_time) {
                    return \Carbon\Carbon::parse($break->break_start_time)->diffInMinutes(
                        \Carbon\Carbon::parse($break->break_end_time)
                    );
                }
                return 0;
            });

            // 勤務時間の計算（出勤時間があり、退勤時間もある場合のみ）
            $workMinutes = 0;
            if ($attendance->start_time && $attendance->end_time) {
                $workMinutes = \Carbon\Carbon::parse($attendance->start_time)->diffInMinutes(
                    \Carbon\Carbon::parse($attendance->end_time)
                ) - $totalBreakMinutes;
            }

            // 休憩時間 & 勤務時間をフォーマットして追加
            $attendance->total_break_time = floor($totalBreakMinutes / 60) . ':' . str_pad($totalBreakMinutes % 60, 2, '0', STR_PAD_LEFT);
            $attendance->work_time = $workMinutes > 0 ? (floor($workMinutes / 60) . ':' . str_pad($workMinutes % 60, 2, '0', STR_PAD_LEFT)) : '-';
        }

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
}
