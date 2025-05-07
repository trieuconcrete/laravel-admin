<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            [
                'name' => 'Giám đốc',
                'code' => 'GD',
                'description' => 'Giám đốc điều hành công ty',
                'salary_min' => 30000000,
                'salary_max' => 80000000,
                'department' => 'Ban giám đốc',
                'is_active' => true
            ],
            [
                'name' => 'Trưởng phòng',
                'code' => 'TP',
                'description' => 'Trưởng phòng các bộ phận',
                'salary_min' => 15000000,
                'salary_max' => 40000000,
                'department' => 'Quản lý',
                'is_active' => true
            ],
            [
                'name' => 'Kế toán',
                'code' => 'KT',
                'description' => 'Kế toán viên',
                'salary_min' => 8000000,
                'salary_max' => 15000000,
                'department' => 'Tài chính - Kế toán',
                'is_active' => true
            ],
            [
                'name' => 'Tài xế',
                'code' => 'TX',
                'description' => 'Tài xế lái xe vận tải',
                'salary_min' => 7000000,
                'salary_max' => 20000000,
                'department' => 'Vận hành',
                'is_active' => true
            ],
            [
                'name' => 'Nhân viên hành chính',
                'code' => 'NV',
                'description' => 'Nhân viên hành chính văn phòng',
                'salary_min' => 6000000,
                'salary_max' => 12000000,
                'department' => 'Hành chính',
                'is_active' => true
            ],
            [
                'name' => 'Điều phối viên',
                'code' => 'DP',
                'description' => 'Điều phối hành trình và lịch trình vận chuyển',
                'salary_min' => 9000000,
                'salary_max' => 18000000,
                'department' => 'Vận hành',
                'is_active' => true
            ],
            [
                'name' => 'Nhân viên kho',
                'code' => 'NK',
                'description' => 'Nhân viên quản lý kho hàng',
                'salary_min' => 6000000,
                'salary_max' => 10000000,
                'department' => 'Kho vận',
                'is_active' => true
            ],
            [
                'name' => 'Kỹ thuật viên',
                'code' => 'KTV',
                'description' => 'Kỹ thuật viên bảo dưỡng và sửa chữa phương tiện',
                'salary_min' => 8000000,
                'salary_max' => 15000000,
                'department' => 'Kỹ thuật',
                'is_active' => true
            ],
            [
                'name' => 'Chăm sóc khách hàng',
                'code' => 'CSKH',
                'description' => 'Nhân viên chăm sóc khách hàng',
                'salary_min' => 7000000,
                'salary_max' => 12000000,
                'department' => 'Kinh doanh',
                'is_active' => true
            ],
            [
                'name' => 'Phụ xe',
                'code' => 'PX',
                'description' => 'Phụ xe hỗ trợ tài xế',
                'salary_min' => 5000000,
                'salary_max' => 8000000,
                'department' => 'Vận hành',
                'is_active' => true
            ],
        ];

        foreach ($positions as $key => $position) {
            $position['id'] = $key + 1;
            Position::create($position);
        }
    }
}
