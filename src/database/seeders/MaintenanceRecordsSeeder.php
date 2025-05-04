<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;

class MaintenanceRecordsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Đảm bảo đã có vehicles
        if (Vehicle::count() === 0) {
            $this->call(VehiclesSeeder::class);
        }

        $vehicles = Vehicle::all();

        foreach ($vehicles as $vehicle) {
            // Mỗi vehicle có 1-5 maintenance records
            $recordCount = rand(1, 5);
            
            for ($i = 0; $i < $recordCount; $i++) {
                // 60% completed, 20% scheduled, 10% in progress, 10% cancelled
                $random = rand(1, 100);
                
                if ($random <= 60) {
                    MaintenanceRecord::factory()->completed()->create([
                        'vehicle_id' => $vehicle->vehicle_id
                    ]);
                } elseif ($random <= 80) {
                    MaintenanceRecord::factory()->scheduled()->create([
                        'vehicle_id' => $vehicle->vehicle_id
                    ]);
                } elseif ($random <= 90) {
                    MaintenanceRecord::factory()->inProgress()->create([
                        'vehicle_id' => $vehicle->vehicle_id
                    ]);
                } else {
                    MaintenanceRecord::factory()->cancelled()->create([
                        'vehicle_id' => $vehicle->vehicle_id
                    ]);
                }
            }
        }

        // Tạo một số maintenance records cụ thể cho demo
        $demoVehicle = Vehicle::first();
        
        if ($demoVehicle) {
            // Bảo dưỡng định kỳ đã hoàn thành
            MaintenanceRecord::create([
                'vehicle_id' => $demoVehicle->vehicle_id,
                'maintenance_type' => MaintenanceRecord::TYPE_ROUTINE,
                'description' => 'Bảo dưỡng định kỳ 20.000 km',
                'start_date' => now()->subMonths(3),
                'end_date' => now()->subMonths(3)->addDays(1),
                'cost' => 2500000,
                'service_provider' => 'Garage Minh Phát',
                'status' => MaintenanceRecord::STATUS_COMPLETED,
                'notes' => 'Đã thay dầu máy, lọc dầu, lọc gió, kiểm tra phanh'
            ]);

            // Thay lốp xe đã hoàn thành
            MaintenanceRecord::create([
                'vehicle_id' => $demoVehicle->vehicle_id,
                'maintenance_type' => MaintenanceRecord::TYPE_TIRE_CHANGE,
                'description' => 'Thay 4 lốp xe mới',
                'start_date' => now()->subMonths(1),
                'end_date' => now()->subMonths(1),
                'cost' => 4800000,
                'service_provider' => 'Garage Minh Phát',
                'status' => MaintenanceRecord::STATUS_COMPLETED,
                'notes' => 'Thay 4 lốp Michelin 195/65R15'
            ]);

            // Bảo dưỡng sắp tới
            MaintenanceRecord::create([
                'vehicle_id' => $demoVehicle->vehicle_id,
                'maintenance_type' => MaintenanceRecord::TYPE_OIL_CHANGE,
                'description' => 'Thay dầu máy định kỳ',
                'start_date' => now()->addDays(7),
                'end_date' => null,
                'cost' => null,
                'service_provider' => 'Garage Thành Đạt',
                'status' => MaintenanceRecord::STATUS_SCHEDULED,
                'notes' => 'Dự kiến thay dầu Castrol 10W-40'
            ]);
        }
    }
}
