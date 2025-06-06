<?php

namespace App\Services;

use App\Repositories\Interface\ShipmentRepositoryInterface;
use App\Models\Shipment;
use App\Models\ShipmentGood;
use App\Models\ShipmentDeduction;
use App\Models\DeductionDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShipmentService
{
    protected $shipmentRepository;

    public function __construct(ShipmentRepositoryInterface $shipmentRepository)
    {
        $this->shipmentRepository = $shipmentRepository;
    }

    public function list($filters = [], $perPage = 20)
    {
        return $this->shipmentRepository->getShipmentsWithFilters($filters, $perPage);
    }

    public function find($id)
    {
        return $this->shipmentRepository->find($id)->with(['driver', 'vehicle', 'goods', 'shipmentDeductions.shipmentDeductionType']);
    }

    public function createShipment($data)
    {
        Log::info($data);
        return DB::transaction(function () use ($data) {
            // 1. Tạo shipment chính
            $shipmentData = [
                'shipment_code' => Shipment::generateShipmentCode(),
                'customer_id' => $data['customer_id'],
                'vehicle_id' => $data['vehicle_id'],
                'origin' => $data['origin'],
                'destination' => $data['destination'],
                'departure_time' => $data['departure_time'],
                'estimated_arrival_time' => $data['estimated_arrival_time'] ?? null,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'],
                'distance' => $data['distance'] ?? null,
            ];
            $shipment = Shipment::create($shipmentData);

            // 2. Lưu các chi phí chuyến hàng (ShipmentDeduction)
            if (!empty($data['deductions'])) {
                foreach ($data['deductions'] as $deduction_type_id => $amount) {
                    // Kiểm tra xem deduction_type_id có phải là số nguyên dương và amount có giá trị
                    if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0 && $amount !== null && $amount !== '' && is_numeric($amount)) {
                        ShipmentDeduction::create([
                            'shipment_id' => $shipment->id,
                            'shipment_deduction_type_id' => (int)$deduction_type_id,
                            'amount' => (float)$amount,
                        ]);
                    }
                }
            }

            // 3. Lưu danh sách hàng hóa (ShipmentGood)
            if (!empty($data['goods'])) {
                foreach ($data['goods'] as $good) {
                    ShipmentGood::create([
                        'shipment_id' => $shipment->id,
                        'name' => $good['name'],
                        'quantity' => $good['quantity'],
                        'unit' => $good['unit'],
                        'notes' => $good['notes'] ?? null,
                        'weight' => $good['weight'] ?? null,
                    ]);
                }
            }

            // 4. Lưu các phụ cấp tài xế/lơ xe
            if (!empty($data['drivers'])) {
                foreach ($data['drivers'] as $person) {
                    // Kiểm tra user_id có tồn tại và là số nguyên dương
                    if (isset($person['user_id']) && is_numeric($person['user_id']) && (int)$person['user_id'] > 0) {
                        $user_id = (int)$person['user_id'];
                        
                        if (!empty($person['deductions'])) {
                            foreach ($person['deductions'] as $deduction_type_id => $amount) {
                                // Kiểm tra deduction_type_id và amount có hợp lệ
                                if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0 && 
                                    $amount !== null && $amount !== '' && is_numeric($amount)) {
                                    ShipmentDeduction::create([
                                        'user_id' => $user_id,
                                        'shipment_id' => $shipment->id,
                                        'shipment_deduction_type_id' => (int)$deduction_type_id ?? null,
                                        'amount' => (float)$amount ?? null,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            return $shipment;
        });
    }

    public function update(Shipment $shipment, array $data)
    {
        Log::info('Dữ liệu cập nhật shipment: ' . json_encode($data));
        return DB::transaction(function () use ($shipment, $data) {
            // 1. Cập nhật thông tin cơ bản của shipment
            $shipmentData = $data;
            unset($shipmentData['goods'], $shipmentData['deductions'], $shipmentData['drivers']);
            $shipment->update($shipmentData);
            
            // 2. Xóa và cập nhật lại các chi phí chuyến hàng (ShipmentDeduction)
            $shipment->shipmentDeductions()->where('user_id', null)->delete();
            
            if (!empty($data['deductions'])) {
                foreach ($data['deductions'] as $deduction_type_id => $amount) {
                    // Kiểm tra xem deduction_type_id có phải là số nguyên dương và amount có giá trị
                    if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0 && $amount !== null && $amount !== '' && is_numeric($amount)) {
                        ShipmentDeduction::create([
                            'shipment_id' => $shipment->id,
                            'shipment_deduction_type_id' => (int)$deduction_type_id,
                            'amount' => (float)$amount,
                        ]);
                    }
                }
            }
            
            // 3. Xóa và cập nhật lại danh sách hàng hóa (ShipmentGood)
            $shipment->goods()->delete();
            
            if (!empty($data['goods'])) {
                foreach ($data['goods'] as $good) {
                    ShipmentGood::create([
                        'shipment_id' => $shipment->id,
                        'name' => $good['name'],
                        'quantity' => $good['quantity'],
                        'unit' => $good['unit'],
                        'notes' => $good['notes'] ?? null,
                        'weight' => $good['weight'] ?? null,
                    ]);
                }
            }
            
            // 4. Xóa và cập nhật lại các phụ cấp tài xế/lơ xe
            $shipment->shipmentDeductions()->whereNotNull('user_id')->delete();
            
            if (!empty($data['drivers'])) {
                foreach ($data['drivers'] as $person) {
                    // Kiểm tra user_id có tồn tại và là số nguyên dương
                    if (isset($person['user_id']) && is_numeric($person['user_id']) && (int)$person['user_id'] > 0) {
                        $user_id = (int)$person['user_id'];
                        
                        if (!empty($person['deductions'])) {
                            foreach ($person['deductions'] as $deduction_type_id => $amount) {
                                // Kiểm tra deduction_type_id và amount có hợp lệ
                                if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0) {
                                    ShipmentDeduction::create([
                                        'user_id' => $user_id,
                                        'shipment_id' => $shipment->id,
                                        'shipment_deduction_type_id' => (int)$deduction_type_id ?? null,
                                        'amount' => (float)$amount ?? null,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            
            return $shipment->refresh();
        });
    }

    public function delete(Shipment $shipment)
    {
        $shipment->shipmentDeductions()->delete();
        $shipment->goods()->delete();
        
        return $this->shipmentRepository->delete($shipment);
    }
}
