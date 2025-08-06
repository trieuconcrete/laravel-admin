<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Di chuyển dữ liệu từ car_rental_vehicle_logs sang shipments
        $vehicleLogs = DB::table('car_rental_vehicle_logs')->get();
        
        foreach ($vehicleLogs as $log) {
            // Lấy thông tin car_rental để lấy customer_id
            $carRental = DB::table('car_rentals')->where('id', $log->car_rental_id)->first();
            
            if ($carRental) {
                // Tạo shipment code unique
                $shipmentCode = 'SHP' . now()->format('ymd') . strtoupper(substr(md5(microtime() . $log->id), 0, 4));
                
                // Ensure unique shipment code
                while (DB::table('shipments')->where('shipment_code', $shipmentCode)->exists()) {
                    $shipmentCode = 'SHP' . now()->format('ymd') . strtoupper(substr(md5(microtime() . rand()), 0, 4));
                }
                
                $shipmentData = [
                    'shipment_code' => $shipmentCode,
                    'customer_id' => $carRental->customer_id,
                    'origin' => $log->start_location ?: 'Điểm bắt đầu',
                    'destination' => $log->end_location ?: 'Điểm kết thúc',
                    'departure_time' => $log->run_date . ' ' . $log->start_time,
                    'estimated_arrival_time' => $log->run_date . ' ' . $log->end_time,
                    'cargo_description' => 'Thuê xe - Di chuyển từ nhật ký xe',
                    'driver_id' => $log->driver_id,
                    'vehicle_id' => $log->vehicle_id,
                    'distance' => $log->total_distance ?: 0,
                    'status' => 'completed',
                    'is_car_rental' => true,
                    'shipment_type' => 2, // SHIPMENT_TYPE_MONTHLY_RENTAL
                    'created_by' => 1, // Default admin
                    
                    // Thông tin từ vehicle log
                    'car_rental_id' => $log->car_rental_id,
                    'start_time' => $log->start_time,
                    'end_time' => $log->end_time,
                    'run_date' => $log->run_date,
                    'overtime_hours' => $log->overtime_hours ?: 0,
                    'start_odometer' => $log->start_odometer ?: 0,
                    'end_odometer' => $log->end_odometer ?: 0,
                    'overtime_rate' => $log->overtime_rate ?: 0,
                    'total_overtime_cost' => $log->total_overtime_cost ?: 0,
                    'parking_fee' => $log->parking_fee ?: 0,
                    'notes' => $log->notes,
                    'created_at' => $log->created_at ?: now(),
                    'updated_at' => $log->updated_at ?: now(),
                ];
                
                // Insert shipment
                $shipmentId = DB::table('shipments')->insertGetId($shipmentData);
                
                // Update car_rental_vehicle_log với shipment_id
                DB::table('car_rental_vehicle_logs')
                    ->where('id', $log->id)
                    ->update(['shipment_id' => $shipmentId]);
            }
        }
        
        echo "Migrated " . count($vehicleLogs) . " vehicle logs to shipments successfully.\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa shipments được tạo từ migration này
        DB::table('shipments')
            ->where('shipment_type', 2)
            ->where('is_car_rental', true)
            ->where('cargo_description', 'LIKE', 'Thuê xe - Di chuyển từ nhật ký xe%')
            ->delete();
            
        // Reset shipment_id trong car_rental_vehicle_logs
        DB::table('car_rental_vehicle_logs')->update(['shipment_id' => null]);
        
        echo "Rolled back vehicle log migration.\n";
    }
}; 