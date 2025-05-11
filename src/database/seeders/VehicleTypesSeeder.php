<?php

// database/seeders/VehicleTypesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleTypes = [
            [
                'name' => 'Xe tải nhỏ',
                'description' => 'Xe tải có tải trọng dưới 2.5 tấn, phù hợp cho vận chuyển hàng hóa trong thành phố'
            ],
            [
                'name' => 'Xe tải trung',
                'description' => 'Xe tải có tải trọng từ 2.5 - 8 tấn, phù hợp cho vận chuyển hàng hóa khoảng cách trung bình'
            ],
            [
                'name' => 'Xe tải lớn',
                'description' => 'Xe tải có tải trọng từ 8 - 15 tấn, phù hợp cho vận chuyển hàng hóa đường dài'
            ],
            [
                'name' => 'Xe tải thùng đông lạnh',
                'description' => 'Xe tải có hệ thống làm lạnh, phù hợp vận chuyển thực phẩm và hàng đông lạnh'
            ],
            [
                'name' => 'Xe đầu kéo',
                'description' => 'Xe đầu kéo container, phù hợp cho vận chuyển hàng hóa quốc tế'
            ],
            [
                'name' => 'Container 20 feet',
                'description' => 'Container tiêu chuẩn 20 feet (6.1m), sức chứa khoảng 28-30 CBM'
            ],
            [
                'name' => 'Container 40 feet',
                'description' => 'Container tiêu chuẩn 40 feet (12.2m), sức chứa khoảng 58-60 CBM'
            ],
            [
                'name' => 'Container 40 feet HC',
                'description' => 'Container 40 feet cao (High Cube), sức chứa khoảng 68-70 CBM'
            ],
            [
                'name' => 'Container lạnh',
                'description' => 'Container có hệ thống làm lạnh cho hàng hóa đặc biệt'
            ],
            [
                'name' => 'Xe tải cẩu',
                'description' => 'Xe tải có gắn cẩu để nâng hạ hàng hóa nặng'
            ],
            [
                'name' => 'Xe ben',
                'description' => 'Xe tải chuyên dụng cho vận chuyển vật liệu xây dựng, cát, đá'
            ],
            [
                'name' => 'Xe bồn',
                'description' => 'Xe chở chất lỏng như xăng dầu, hóa chất, nước'
            ],
            [
                'name' => 'Xe chuyên dụng',
                'description' => 'Các loại xe chuyên dụng khác như xe chở rác, xe cứu hỏa'
            ],
            [
                'name' => 'Xe van',
                'description' => 'Xe van vận chuyển hàng hóa nhỏ trong đô thị'
            ],
            [
                'name' => 'Xe máy',
                'description' => 'Xe máy chuyển phát nhanh cho các gói hàng nhỏ'
            ]
        ];

        foreach ($vehicleTypes as $vehicleType) {
            DB::table('vehicle_types')->insert([
                'name' => $vehicleType['name'],
                'description' => $vehicleType['description'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}