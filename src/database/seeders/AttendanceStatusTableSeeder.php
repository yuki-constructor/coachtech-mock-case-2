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
        $statuses = ['勤務外', '勤務中', '休憩中'];
      
        foreach ($statuses as $status) {
            AttendanceStatus::create(['status' => $status]);
        }
    }
}
