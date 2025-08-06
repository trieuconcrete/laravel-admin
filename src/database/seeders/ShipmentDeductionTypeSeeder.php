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
            'BỐC XẾP', 'PHỤ THU KẾT HỢP', 'PHÍ CẦU ĐƯỜNG', 'PHÍ KHÁC',
        ];
        $driverAndBusboyTypes = [
            'PHỤ CẤP TÀI 2', 'PHỤ CẤP TÀI 3', 'BỐC XẾP', 'PHÍ CẦU ĐƯỜNG', 'PHỤ CẤP CHỦ NHẬT', 'PHỤ CẤP ĐI XA', 'PHỤ CẤP SỚM/ĐÊM', 'PHỤ CẤP CƠM TỐI', 'PHỤ CẤP KHÁC',
        ];

        foreach ($expenseTypes as $key => $name) {
            ShipmentDeductionType::create([
                'name' => $name,
                'type' => ShipmentDeductionType::TYPE_EXPENSE,
                'status' => 'active',
                'order' => $key + 1,
            ]);
        }
        foreach ($driverAndBusboyTypes as $key => $name) {
            ShipmentDeductionType::create([
                'name' => $name,
                'type' => ShipmentDeductionType::TYPE_DRIVER,
                'status' => 'active',
                'order' => $key + 1,
            ]);
        }


        $busDriverTypes = [
            'BỐC XẾP', 'PHÍ CẦU ĐƯỜNG', 'PHỤ CẤP CHỦ NHẬT', 'PHỤ CẤP ĐI XA', 'PHỤ CẤP SỚM/ĐÊM', 'PHỤ CẤP LƠ', 'PHỤ CẤP CƠM TỐI', 'PHỤ CẤP KHÁC',
        ];
        foreach ($busDriverTypes as $key => $name) {
            ShipmentDeductionType::create([
                'name' => $name,
                'type' => ShipmentDeductionType::TYPE_BUS_DRIVER,
                'status' => 'active',
                'order' => $key + 1,
            ]);
        }
    }
}
