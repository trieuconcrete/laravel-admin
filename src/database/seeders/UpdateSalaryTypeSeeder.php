<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enum\SalaryType;

class UpdateSalaryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cập nhật tất cả user hiện có có role driver thành salary_type = 1 (lương cơ bản)
        User::where('role', User::ROLE_DRIVER)
            ->update(['salary_type' => SalaryType::BASIC_SALARY->value]);

        // Cập nhật tất cả user khác thành salary_type = 1 (mặc định)
        User::where('role', '!=', User::ROLE_DRIVER)
            ->update(['salary_type' => SalaryType::BASIC_SALARY->value]);

        $this->command->info('Đã cập nhật salary_type cho tất cả users!');
    }
} 