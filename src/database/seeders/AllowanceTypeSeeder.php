<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AllowanceType;

class AllowanceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AllowanceType::truncate();
        $allowanceTypes = [
            [
                'type_name' => 'Phụ cấp xăng xe',
                'description' => 'Phụ cấp đi lại cho nhân viên',
                'is_taxable' => false,
                'status' => 'active',
            ],
            [
                'type_name' => 'Phụ cấp ăn trưa',
                'description' => 'Phụ cấp ăn trưa hàng tháng',
                'is_taxable' => false,
                'status' => 'active',
            ],
            [
                'type_name' => 'Phụ cấp điện thoại',
                'description' => 'Phụ cấp cước điện thoại',
                'is_taxable' => false,
                'status' => 'active',
            ],
            [
                'type_name' => 'Phụ cấp trách nhiệm',
                'description' => 'Phụ cấp cho vị trí quản lý',
                'is_taxable' => true,
                'status' => 'active',
            ],
            [
                'type_name' => 'Phụ cấp đặc thù tài xế',
                'description' => 'Phụ cấp cho tài xế chạy đường dài/nguy hiểm',
                'is_taxable' => false,
                'status' => 'active',
            ],
            [
                'type_name' => 'Phụ cấp chuyên cần',
                'description' => 'Phụ cấp cho nhân viên không nghỉ phép trong tháng',
                'is_taxable' => false,
                'status' => 'active',
            ],
        ];

        foreach ($allowanceTypes as $type) {
            AllowanceType::create($type);
        }
    }
}
