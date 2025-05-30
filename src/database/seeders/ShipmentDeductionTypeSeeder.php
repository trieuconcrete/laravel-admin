<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShipmentDeductionType;

class ShipmentDeductionTypeSeeder extends Seeder
{
    public function run()
    {
        $expenseTypes = [
            'Bốc xếp', 'Neo xe', 'Công an', 'Cầu Đường', 'Tiền tự cầu', 'Khác',
        ];
        $driverAndBusboyTypes = [
            'Phụ cấp cơm trưa', 'Phụ cấp cơm tối', 'Phụ cấp chủ nhật', 'phụ cấp đi sớm', 'phụ cấp về khuya', 'phụ cấp lễ', 'phụ cấp khác',
        ];

        foreach ($expenseTypes as $name) {
            ShipmentDeductionType::create([
                'name' => $name,
                'type' => 'expense',
                'status' => 'active',
            ]);
        }
        foreach ($driverAndBusboyTypes as $name) {
            ShipmentDeductionType::create([
                'name' => $name,
                'type' => 'driver_and_busboy',
                'status' => 'active',
            ]);
        }
    }
}
