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
        $statuses = [AttendanceRequestStatus::STATUS_APPROVED, AttendanceRequestStatus::STATUS_PENDING_APPROVAL];

        foreach ($statuses as $status) {
            AttendanceRequestStatus::create(['request_status' => $status]);
        }
    }
}
