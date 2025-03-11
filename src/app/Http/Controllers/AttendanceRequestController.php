<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceRequestRequest;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Models\AttendanceRequestBreak;
use App\Models\AttendanceRequestStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceRequestController extends Controller
{
    /**
     * ==============================
     * 従業員ユーザーの勤怠修正申請関連
     * ==============================
     */

    /**
     * 勤怠修正申請を処理
     * @route POST /employee/attendance/{attendanceId}/request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function attendanceRequest(AttendanceRequestRequest $request, $attendanceId)
    {
        // 勤怠データ取得（認証ユーザーに紐づいているかチェック）
        $attendance = Attendance::findOrFail($attendanceId);

        // AttendanceRequestStatusモデルでステータスを定数化。attendance_request_statusesテーブルから「承認待ち」のレコードを取得
        $pendingStatus = AttendanceRequestStatus::where('request_status', AttendanceRequestStatus::STATUS_PENDING_APPROVAL)->first();

        // リクエストの日時を `Y-m-d H:i:s` に変換
        $date = $attendance->date; // 勤怠の日付を取得

        $startDateTime = Carbon::parse("{$date} {$request->start_time}");
        $endDateTime = Carbon::parse("{$date} {$request->end_time}");

        // 勤怠修正申請を登録
        $attendanceRequest = AttendanceRequest::create([
            'attendance_id' => $attendance->id,
            'attendance_request_status_id' => $pendingStatus->id,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'reason' => $request->reason,
        ]);

        // 休憩修正申請の登録
        foreach ($request->breaks as $breakId => $break) {
            $breakStartDateTime = Carbon::parse("{$date} {$break['start']}");
            $breakEndDateTime = Carbon::parse("{$date} {$break['end']}");

            AttendanceRequestBreak::create([
                'attendance_request_id' => $attendanceRequest->id,
                'break_id' => $breakId,
                'break_start_time' => $breakStartDateTime,
                'break_end_time' => $breakEndDateTime,
            ]);
        };

        // 申請一覧画面（承認待ち）へリダイレクト
        return redirect()->route('employee.attendance.request.list.pending');
    }

    /**
     * 勤怠申請一覧画面を表示
     * @route GET /employee/attendance/request/list/pending
     * @return \Illuminate\View\View
     */
    public function attendanceRequestListPending()
    {

        $employee = Auth::guard('employee')->user();

        // AttendanceRequestStatusモデルでステータスを定数化。attendance_request_statusesテーブルから「承認待ち」のidを取得
        $pendingStatus = AttendanceRequestStatus::where('request_status', AttendanceRequestStatus::STATUS_PENDING_APPROVAL)->first()->id;

        // ログイン中の従業員の承認待ち申請を取得
        $attendanceRequests = AttendanceRequest::where('attendance_request_status_id', $pendingStatus)
            ->whereHas('attendance', function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })
            ->with(['attendance', 'attendance.employee', 'attendanceRequestStatus'])
            ->orderByDesc('created_at')
            ->get();

        return view('attendance.employee.attendance-request-list-pending', compact('attendanceRequests', 'employee'));
    }
}
