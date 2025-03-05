<?php

namespace Database\Seeders;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employees = [
            ['name' => '西 伶奈', 'email' => 'reina.n@coachtech.com'],
            ['name' => '山田 太郎', 'email' => 'taro.y@coachtech.com'],
            ['name' => '増田 一世', 'email' => 'issei.m@coachtech.com'],
            ['name' => '山本 敬吉', 'email' => 'keikichi.y@coachtech.com'],
            ['name' => '秋田 朋美', 'email' => 'tomomi.a@coachtech.com'],
            ['name' => '中西 敬夫', 'email' => 'norio.n@coachtech.com'],
        ];

        foreach ($employees as $employee) {
            Employee::create([
                'name' => $employee['name'],
                'email' => $employee['email'],
                'email_verified_at' => Carbon::parse('2023-06-01 00:00:01'),
                'password' => Hash::make('123456789'),
            ]);
        }
    }
}
