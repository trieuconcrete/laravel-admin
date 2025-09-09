<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShipmentDeductionType;

class ShipmentDeductionTypeSeeder extends Seeder
{
    public function run()
    {
        // Skip truncation if called from DataSeederMaster (tables already truncated with FK checks disabled)
        if (!app()->has('seeder.skip_truncate')) {
            ShipmentDeductionType::truncate();
        }
        
        ShipmentDeductionType::whereNotNull('id')->update([
            'status' => 'inactive',
        ]);
        // Chi phí vận chuyển
        $expenseTypes = [
            'BỐC XẾP', 'PHỤ THU KẾT HỢP', 'PHÍ CẦU ĐƯỜNG', 'PHÍ KHÁC',
        ];
        // Phụ cấp cho tài xế
        $driverAndBusboyTypes = [
            'PHỤ CẤP TÀI 2', 'PHỤ CẤP TÀI 3', 'BỐC XẾP', 'PHÍ CẦU ĐƯỜNG', 'PHỤ CẤP CHỦ NHẬT', 'PHỤ CẤP ĐI XA', 'PHỤ CẤP SỚM/ĐÊM', 'PHỤ CẤP CƠM TỐI', 'PHỤ CẤP CƠM NGÀY', 'PHỤ CẤP KHÁC',
        ];
        // Chi phí thuê xe
        $carRentalexpenseTypes = [
            'BỐC XẾP', 'PHỤ THU KẾT HỢP', 'PHÍ CẦU ĐƯỜNG', 'PHÍ KHÁC',
        ];

        foreach ($expenseTypes as $key => $name) {
            ShipmentDeductionType::updateOrCreate([
                'name' => $name,
                'type' => ShipmentDeductionType::TYPE_EXPENSE,
            ], [
                'status' => 'active',
                'order' => $key + 1,
            ]);
        }

        foreach ($carRentalexpenseTypes as $key => $name) {
            ShipmentDeductionType::updateOrCreate([
                'name' => $name,
                'type' => ShipmentDeductionType::TYPE_CAR_RENTAL_EXPENSE,
            ], [
                'status' => 'active',
                'order' => $key + 1,
            ]);
        }

        foreach ($driverAndBusboyTypes as $key => $name) {
            ShipmentDeductionType::updateOrCreate([
                'name' => $name,
                'type' => ShipmentDeductionType::TYPE_DRIVER,
            ], [
                'status' => 'active',
                'order' => $key + 1,
            ]);
        }


        $busDriverTypes = [
            'BỐC XẾP', 'PHÍ CẦU ĐƯỜNG', 'PHỤ CẤP CHỦ NHẬT', 'PHỤ CẤP ĐI XA', 'PHỤ CẤP SỚM/ĐÊM', 'PHỤ CẤP LƠ', 'PHỤ CẤP CƠM TỐI', 'PHỤ CẤP CƠM NGÀY', 'PHỤ CẤP KHÁC',
        ];
        foreach ($busDriverTypes as $key => $name) {
            ShipmentDeductionType::updateOrCreate([
                'name' => $name,
                'type' => ShipmentDeductionType::TYPE_BUS_DRIVER,
            ], [
                'status' => 'active',
                'order' => $key + 1,
            ]);
        }
    }
}
