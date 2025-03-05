<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceStatus;
use App\Models\BreakModel;
use App\Models\Employee;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //AttendanceStatusモデルでステータスを定数化。attendance_statusesテーブルから「勤務外」のステータス ID を取得
        $statusOffId = AttendanceStatus::where('status', AttendanceStatus::STATUS_OFF)->first()->id;

        // 全従業員を取得
        $employees = Employee::all();

        // 2025-02-01 から 2025-04-30 までの期間を作成
        $period = CarbonPeriod::create('2025-02-01', '2025-04-30');

        foreach ($employees as $employee) {
            foreach ($period as $date) {
                // 出勤・退勤の日時を作成
                $startTime = $date->copy()->setTime(9, 0, 0); // 2025-02-01 09:00:00
                $endTime = $date->copy()->setTime(18, 0, 0);

                // 勤怠情報を登録
                $attendance = Attendance::create([
                    'employee_id' => $employee->id,
                    'attendance_status_id' => $statusOffId,
                    'date' => $date->toDateString(),
                    'start_time' => $startTime->toDateTimeString(),
                    'end_time' => $endTime->toDateTimeString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 休憩の日時を作成
                $breakStartTime = $date->copy()->setTime(12, 0, 0);
                $breakEndTime = $date->copy()->setTime(13, 0, 0);

                // 休憩情報を登録
                BreakModel::create([
                    'attendance_id' => $attendance->id,
                    'break_start_time' => $breakStartTime->toDateTimeString(),
                    'break_end_time' => $breakEndTime->toDateTimeString(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
