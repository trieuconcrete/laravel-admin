<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShipmentDeductionType;

class ShipmentDeductionTypeSeeder extends Seeder
{
    public function run()
    {
        ShipmentDeductionType::truncate();
        ShipmentDeductionType::whereNotNull('id')->update([
            'status' => 'inactive',
        ]);

        $expenseTypes = [
            'BỐC XẾP', 'CHI PHÍ CẦU ĐƯỜNG', 'CHI PHÍ KHÁC',
        ];
        $driverAndBusboyTypes = [
            'PHỤ CẤP TÀI 2', 'PHỤ CẤP TÀI 3', 'PHỤ CẤP CHỦ NHẬT', 'PHỤ CẤP ĐI XA', 'PHỤ CẤP SỚM/ĐÊM', 'PHỤ CẤP CƠM TỐI', 'PHỤ CẤP KHÁC',
        ];

        foreach ($expenseTypes as $name) {
            ShipmentDeductionType::create([
                'name' => $name,
                'type' => ShipmentDeductionType::TYPE_EXPENSE,
                'status' => 'active',
            ]);
        }
        foreach ($driverAndBusboyTypes as $name) {
            ShipmentDeductionType::create([
                'name' => $name,
                'type' => ShipmentDeductionType::TYPE_DRIVER,
                'status' => 'active',
            ]);
        }


        $busDriverTypes = [
            'PHỤ CẤP CHỦ NHẬT', 'PHỤ CẤP ĐI XA', 'PHỤ CẤP SỚM/ĐÊM', 'PHỤ CẤP LƠ', 'PHỤ CẤP CƠM TỐI', 'PHỤ CẤP KHÁC',
        ];
        foreach ($busDriverTypes as $name) {
            ShipmentDeductionType::create([
                'name' => $name,
                'type' => ShipmentDeductionType::TYPE_BUS_DRIVER,
                'status' => 'active',
            ]);
        }
    }
}
