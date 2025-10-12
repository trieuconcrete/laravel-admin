<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DriverSeeder extends Seeder
{
    public function run()
    {
        $bcryptOptions = ['rounds' => 12];
        $now = Carbon::now();

        $drivers = [];

        // Ví dụ tạo 10 tài xế
        for ($i = 0; $i < 10; $i++) {
            $employee_code = 'TX' . str_pad($i + 3, 3, '0', STR_PAD_LEFT); // TX003, TX004,...
            $phone = '0' . rand(100000000, 999999999); // 10 số
            $id_number = (string) rand(100000000000, 999999999999); // CCCD/CMND

            $drivers[] = [
                'full_name' => "Tài xế $employee_code",
                'employee_code' => $employee_code,
                'username' => "driver" . ($i + 1),
                'email' => "driver" . ($i + 1) . "@example.com",
                'birthday' => Carbon::parse('1985-01-01')->addYears(rand(20, 15))->toDateString(),
                'position_id' => 4,
                'department_id' => rand(1, 5),
                'email_verified_at' => $now,
                'password' => Hash::make('password123', $bcryptOptions),
                'failed_attempts' => 0,
                'is_locked' => 0,
                'locked_until' => null,
                'phone' => $phone,
                'id_number' => $id_number,
                'id_number_issuance_date' => Carbon::parse('2010-01-01')->addYears(rand(0, 10))->toDateString(),
                'address' => "Số nhà " . ($i + 1) . ", Quận " . rand(1, 12) . ", TP.HCM",
                'join_date' => Carbon::parse('2020-01-01')->addMonths(rand(0, 24))->toDateString(),
                'role' => 'driver',
                'avatar' => null,
                'profile_image' => null,
                'salary_base' => rand(5_000_000, 15_000_000),
                'gender' => rand(0, 1),
                'notes' => null,
                'deleted_at' => null,
                'remember_token' => Str::random(10),
                'created_at' => $now,
                'updated_at' => $now,
                'salary_advance_amount' => rand(1_000_000, 5_000_000),
                'salary_type' => rand(1, 2),
                'salary_by_percent' => rand(50, 100),
                'has_insurance' => rand(0, 1),
                'insurance_start_date' => Carbon::parse('2021-01-01')->addMonths(rand(0, 12))->toDateString(),
                'social_insurance_amount' => rand(1_000_000, 5_000_000),
                'social_insurance_number' => (string) rand(1000000000, 9999999999),
                'status' => 1,
            ];
        }

        DB::table('users')->insert($drivers);
    }
}
