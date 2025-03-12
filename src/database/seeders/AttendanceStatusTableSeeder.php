<?php

namespace Database\Seeders;

use App\Models\AttendanceStatus;
use Illuminate\Database\Seeder;

class AttendanceStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // AttendanceStatusモデルでステータス'勤務外', '勤務中', '休憩中'を定数化。AttendanceStatusモデルから呼び出し。
        $statuses = [
            AttendanceStatus::STATUS_OFF,
            AttendanceStatus::STATUS_ON,
            AttendanceStatus::STATUS_BREAK,
        ];

        foreach ($statuses as $status) {
            AttendanceStatus::create(['status' => $status]);
        }
    }
}
