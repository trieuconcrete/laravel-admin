<?php

namespace Database\Seeders;

use App\Models\VehicleDocument;
use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleDocumentsSeeder extends Seeder
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
            // Mỗi xe có các loại giấy tờ cơ bản
            $this->createDocumentsForVehicle($vehicle);
        }

        // Tạo một số document cụ thể cho demo
        $demoVehicle = Vehicle::first();
        
        if ($demoVehicle) {
            // Đăng kiểm sắp hết hạn
            VehicleDocument::create([
                'vehicle_id' => $demoVehicle->vehicle_id,
                'document_type' => VehicleDocument::TYPE_INSPECTION,
                'issue_date' => now()->subMonths(11),
                'expiry_date' => now()->addMonth(),
                'document_number' => 'DKM2024001',
                'document_file' => 'dang_kiem_2024.pdf',
                'status' => VehicleDocument::STATUS_EXPIRING_SOON
            ]);

            // Bảo hiểm hết hạn
            VehicleDocument::create([
                'vehicle_id' => $demoVehicle->vehicle_id,
                'document_type' => VehicleDocument::TYPE_INSURANCE,
                'issue_date' => now()->subYear(),
                'expiry_date' => now()->subWeek(),
                'document_number' => 'BH2023456789',
                'document_file' => 'bao_hiem_2023.pdf',
                'status' => VehicleDocument::STATUS_EXPIRED
            ]);
        }
    }

    /**
     * Create standard documents for a vehicle
     */
    private function createDocumentsForVehicle($vehicle)
    {
        // Đăng ký xe
        VehicleDocument::factory()->create([
            'vehicle_id' => $vehicle->vehicle_id,
            'document_type' => VehicleDocument::TYPE_REGISTRATION,
            'issue_date' => now()->subYears(rand(1, 5)),
            'expiry_date' => now()->addYears(rand(5, 10)),
            'status' => VehicleDocument::STATUS_VALID
        ]);

        // Đăng kiểm
        $inspectionValid = rand(0, 2); // 0: expired, 1: expiring soon, 2: valid
        if ($inspectionValid === 0) {
            VehicleDocument::factory()->expired()->create([
                'vehicle_id' => $vehicle->vehicle_id,
                'document_type' => VehicleDocument::TYPE_INSPECTION
            ]);
        } elseif ($inspectionValid === 1) {
            VehicleDocument::factory()->expiringSoon()->create([
                'vehicle_id' => $vehicle->vehicle_id,
                'document_type' => VehicleDocument::TYPE_INSPECTION
            ]);
        } else {
            VehicleDocument::factory()->valid()->create([
                'vehicle_id' => $vehicle->vehicle_id,
                'document_type' => VehicleDocument::TYPE_INSPECTION
            ]);
        }

        // Bảo hiểm
        $insuranceValid = rand(0, 2); // 0: expired, 1: expiring soon, 2: valid
        if ($insuranceValid === 0) {
            VehicleDocument::factory()->expired()->create([
                'vehicle_id' => $vehicle->vehicle_id,
                'document_type' => VehicleDocument::TYPE_INSURANCE
            ]);
        } elseif ($insuranceValid === 1) {
            VehicleDocument::factory()->expiringSoon()->create([
                'vehicle_id' => $vehicle->vehicle_id,
                'document_type' => VehicleDocument::TYPE_INSURANCE
            ]);
        } else {
            VehicleDocument::factory()->valid()->create([
                'vehicle_id' => $vehicle->vehicle_id,
                'document_type' => VehicleDocument::TYPE_INSURANCE
            ]);
        }

        // Giấy phép vận tải (50% xe có)
        if (rand(0, 1)) {
            VehicleDocument::factory()->create([
                'vehicle_id' => $vehicle->vehicle_id,
                'document_type' => VehicleDocument::TYPE_PERMIT
            ]);
        }

        // Phí đường bộ (70% xe có)
        if (rand(1, 10) <= 7) {
            VehicleDocument::factory()->create([
                'vehicle_id' => $vehicle->vehicle_id,
                'document_type' => VehicleDocument::TYPE_ROAD_TAX
            ]);
        }
    }
}
