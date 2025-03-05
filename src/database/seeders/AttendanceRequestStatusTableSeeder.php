<?php

namespace Database\Seeders;

use App\Models\AttendanceRequestStatus;
use Illuminate\Database\Seeder;

class AttendanceRequestStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['承認待ち', '承認済み'];

        foreach ($statuses as $status) {
            AttendanceRequestStatus::create(['request_status' => $status]);
        }
    }
}
