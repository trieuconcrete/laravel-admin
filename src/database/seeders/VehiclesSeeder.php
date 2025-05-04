<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Đảm bảo đã có vehicle types
        if (VehicleType::count() === 0) {
            $this->call(VehicleTypesSeeder::class);
        }

        // Tạo 50 vehicles ngẫu nhiên
        Vehicle::factory()->count(50)->create();

        // Tạo một số vehicles cụ thể cho mỗi loại
        $vehicleTypes = VehicleType::all();

        foreach ($vehicleTypes as $vehicleType) {
            // Tạo ít nhất 2 vehicles cho mỗi loại
            Vehicle::factory()->count(2)->create([
                'vehicle_type_id' => $vehicleType->vehicle_type_id,
                'status' => 'active'
            ]);
        }

        // Tạo một số vehicles với trạng thái khác nhau
        Vehicle::factory()->count(5)->inMaintenance()->create();
        Vehicle::factory()->count(3)->inactive()->create();
    }
}
