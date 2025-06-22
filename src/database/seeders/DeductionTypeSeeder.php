<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DeductionType;

class DeductionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeductionType::truncate();
        $deductionTypes = [
            [
                'type_name' => 'Bảo hiểm xã hội',
                'description' => 'Khấu trừ BHXH 8% lương cơ bản',
                'is_mandatory' => true,
                'status' => 'active',
            ],
            [
                'type_name' => 'Bảo hiểm y tế',
                'description' => 'Khấu trừ BHYT 1.5% lương cơ bản',
                'is_mandatory' => true,
                'status' => 'active',
            ],
            [
                'type_name' => 'Bảo hiểm thất nghiệp',
                'description' => 'Khấu trừ BHTN 1% lương cơ bản',
                'is_mandatory' => true,
                'status' => 'active',
            ],
            [
                'type_name' => 'Thuế thu nhập cá nhân',
                'description' => 'Khấu trừ thuế TNCN theo quy định',
                'is_mandatory' => true,
                'status' => 'active',
            ],
            [
                'type_name' => 'Tạm ứng lương',
                'description' => 'Khấu trừ cho khoản tạm ứng lương',
                'is_mandatory' => false,
                'status' => 'active',
            ],
            [
                'type_name' => 'Phạt trễ giờ',
                'description' => 'Khấu trừ cho các lần đi trễ',
                'is_mandatory' => false,
                'status' => 'active',
            ],
            [
                'type_name' => 'Phạt vi phạm nội quy',
                'description' => 'Khấu trừ cho vi phạm quy định công ty',
                'is_mandatory' => false,
                'status' => 'active',
            ],
        ];

        foreach ($deductionTypes as $type) {
            DeductionType::create($type);
        }
    }
}
