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

    /**
     * Get a paginated list of shipments with optional filters
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator<\App\Models\Shipment>
     */
    public function getList(array $filters = [], int $perPage = 20): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->shipmentRepository->getShipmentsWithFilters($filters, $perPage);
    }

    /**
     * Find a shipment by ID with relationships
     *
     * @param int $id
     * @return \App\Models\Shipment|null
     */
    public function find(int $id): ?\App\Models\Shipment
    {
        return $this->shipmentRepository->find($id)->with(['driver', 'vehicle', 'goods', 'shipmentDeductions.shipmentDeductionType'])->first();
    }

    /**
     * Tính toán overtime theo yêu cầu issue #180
     * - Thời gian tính OT dựa trên giờ kết thúc thực tế, không phải default 17:30
     * - Thêm tăng ca trưa 1h nếu có chọn checkbox "Có tăng ca trưa"
     */
    private function calculateOvertime($runDate, $startTime, $endTime, $overtimeRate, $isOvertimeAtNoon = false)
    {
        $startDateTime = \Carbon\Carbon::parse($runDate . ' ' . $startTime);
        $endDateTime = \Carbon\Carbon::parse($runDate . ' ' . $endTime);
        
        // Tính tổng thời gian làm việc
        $totalWorkingHours = $startDateTime->floatDiffInRealHours($endDateTime);
        
        // Tính OT dựa trên giờ kết thúc thực tế (không phải 17:30 cố định)
        $overtimeHours = 0;
        $overtimeStart = \Carbon\Carbon::parse($runDate . ' 17:30');
        
        if ($endDateTime->greaterThan($overtimeStart)) {
            $effectiveStart = $startDateTime->greaterThan($overtimeStart) ? $startDateTime : $overtimeStart;
            $overtimeHours = $endDateTime->floatDiffInRealHours($effectiveStart);
        }
        
        // Thêm tăng ca trưa 1h nếu có chọn checkbox
        if ($isOvertimeAtNoon) {
            $overtimeHours += 1;
        }
        
        $totalOvertimeCost = $overtimeRate * $overtimeHours;
        
        return [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'run_date' => $runDate,
            'overtime_hours' => round($overtimeHours, 2),
            'overtime_rate' => $overtimeRate,
            'total_overtime_cost' => round($totalOvertimeCost, 2),
        ];
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
                'origin2' => $data['origin2'] ?? null,
                'destination2' => $data['destination2'] ?? null,
                'origin3' => $data['origin3'] ?? null,
                'destination3' => $data['destination3'] ?? null,
                'company' => $data['company'] ?? null,
                'company2' => $data['company2'] ?? null,
                'company3' => $data['company3'] ?? null,
                'departure_time' => $data['departure_time'],
                'estimated_arrival_time' => $data['estimated_arrival_time'] ?? null,
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'run_date' => $data['run_date'] ?? null,
                'shipment_type' => $data['shipment_type'] ?? Shipment::SHIPMENT_TYPE_PER_TRIP,
                'is_car_rental' => $data['is_car_rental'] ?? false,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'],
                'distance' => $data['distance'] ?? null,
                'unit_price' => $data['unit_price'] ?? null,
                'overtime_rate' => $data['overtime_rate'] ?? 50000,
                'is_overtime_at_noon' => $data['is_overtime_at_noon'] ?? false,
                'created_by' => auth()->id(),
            ];

            $shipment = Shipment::create($shipmentData);

            // Tính toán OT mới theo yêu cầu issue #180
            if (!empty($data['start_time']) && !empty($data['end_time']) && !empty($data['run_date'])) {
                $overtimeData = $this->calculateOvertime($data['run_date'], $data['start_time'], $data['end_time'], $data['overtime_rate'], $data['is_overtime_at_noon'] ?? false);
                $shipment->update($overtimeData);
            }

            // 2. Lưu các chi phí chuyến hàng (ShipmentDeduction)
            if (!empty($data['deductions'])) {
                foreach ($data['deductions'] as $deduction_type_id => $amount) {
                    // Kiểm tra xem deduction_type_id có phải là số nguyên dương và amount có giá trị
                    if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0 && $amount !== null && $amount !== '') {
                        $deductionType = \App\Models\ShipmentDeductionType::find($deduction_type_id);
                        
                        // Nếu là "Ghi chú", lưu vào column notes
                        if ($deductionType && $deductionType->name === 'Ghi chú') {
                            ShipmentDeduction::create([
                                'shipment_id' => $shipment->id,
                                'shipment_deduction_type_id' => (int)$deduction_type_id,
                                'amount' => 0, // Không lưu số tiền cho ghi chú
                                'notes' => (string)$amount, // Lưu ghi chú vào column notes
                            ]);
                        } else {
                            // Nếu là numeric, kiểm tra và lưu dưới dạng float
                            if (is_numeric($amount)) {
                                ShipmentDeduction::create([
                                    'shipment_id' => $shipment->id,
                                    'shipment_deduction_type_id' => (int)$deduction_type_id,
                                    'amount' => (float)$amount,
                                ]);
                            }
                        }
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

            // 4. Lưu các phụ cấp tài xế/lơ xe (chỉ khi không phải xe thuê)
            if (!empty($data['drivers']) && !($data['is_car_rental'] ?? false)) {
                foreach ($data['drivers'] as $person) {
                    // Kiểm tra user_id có tồn tại và là số nguyên dương
                    if (isset($person['user_id']) && is_numeric($person['user_id']) && (int)$person['user_id'] > 0) {
                        $user_id = (int)$person['user_id'];
                        
                        if (!empty($person['deductions'])) {
                            // Extract notes from deductions array if it exists
                            $notes = null;
                            if (isset($person['deductions']['notes'])) {
                                $notes = $person['deductions']['notes'];
                                unset($person['deductions']['notes']); // Remove notes from deductions array
                            }

                            $isMainDriver = false;
                            if (isset($person['deductions']['is_main_driver'])) {
                                $isMainDriver = (bool) $person['deductions']['is_main_driver'];
                                unset($person['deductions']['is_main_driver']); // Remove notes from deductions array
                            }
                            
                            foreach ($person['deductions'] as $deduction_type_id => $amount) {
                                // Kiểm tra deduction_type_id và amount có hợp lệ
                                if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0) {
                                    ShipmentDeduction::create([
                                        'user_id' => $user_id,
                                        'shipment_id' => $shipment->id,
                                        'shipment_deduction_type_id' => (int)$deduction_type_id ?? null,
                                        'amount' => (float)$amount ?? null,
                                        'notes' => $notes, // Add notes field
                                        'is_main_driver' => $isMainDriver
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            // 5. Lưu các phụ cấp tài xế phụ cấp (chỉ khi không phải xe thuê)
            if (!empty($data['driverPXs']) && !($data['is_car_rental'] ?? false)) {
                foreach ($data['driverPXs'] as $driverPX) {
                    // Kiểm tra user_id có tồn tại và là số nguyên dương
                    if (isset($driverPX['user_id']) && is_numeric($driverPX['user_id']) && (int)$driverPX['user_id'] > 0) {
                        $user_id = (int)$driverPX['user_id'];
                        
                        if (!empty($driverPX['deductions'])) {
                            // Extract notes from deductions array if it exists
                            $notes = null;
                            if (isset($driverPX['deductions']['notes'])) {
                                $notes = $driverPX['deductions']['notes'];
                                unset($driverPX['deductions']['notes']); // Remove notes from deductions array
                            }
                            
                            foreach ($driverPX['deductions'] as $deduction_type_id => $amount) {
                                // Kiểm tra deduction_type_id và amount có hợp lệ
                                if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0) {
                                    ShipmentDeduction::create([
                                        'user_id' => $user_id,
                                        'shipment_id' => $shipment->id,
                                        'shipment_deduction_type_id' => (int)$deduction_type_id ?? null,
                                        'amount' => (float)$amount ?? null,
                                        'is_main_driver' => false,
                                        'notes' => $notes, // Add notes field
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            $this->resetMonthlyReportFinalized($shipment->customer_id, $shipment->departure_time);
            

            return $shipment;
        });
    }

    public function update(Shipment $shipment, array $data)
    {
        Log::info('Dữ liệu cập nhật shipment: ' . json_encode($data));
        
        // Debug log cho is_car_rental
        Log::info('is_car_rental debug:', [
            'raw_value' => $data['is_car_rental'] ?? 'NOT_SET',
            'type' => gettype($data['is_car_rental'] ?? 'NOT_SET'),
            'bool_value' => (bool)($data['is_car_rental'] ?? false),
            'will_save_drivers' => !($data['is_car_rental'] ?? false)
        ]);
        
        // Debug log cho drivers data
        if (!empty($data['drivers'])) {
            Log::info('Drivers data trong update:', $data['drivers']);
        }
        return DB::transaction(function () use ($shipment, $data) {
            // Tính toán OT mới theo yêu cầu issue #180
            if (!empty($data['start_time']) && !empty($data['end_time']) && !empty($data['run_date'])) {
                $overtimeData = $this->calculateOvertime($data['run_date'], $data['start_time'], $data['end_time'], $data['overtime_rate'], $data['is_overtime_at_noon'] ?? false);
                $data = array_merge($data, $overtimeData);
            }

            // 1. Cập nhật thông tin cơ bản của shipment
            $shipmentData = $data;
            unset($shipmentData['goods'], $shipmentData['deductions'], $shipmentData['drivers']);
            $shipment->update($shipmentData);
            
            // 2. Xóa và cập nhật lại các chi phí chuyến hàng (ShipmentDeduction)
            $shipment->shipmentDeductions()->where('user_id', null)->delete();
            
            if (!empty($data['deductions'])) {
                foreach ($data['deductions'] as $deduction_type_id => $amount) {
                    // Kiểm tra xem deduction_type_id có phải là số nguyên dương và amount có giá trị
                    if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0 && $amount !== null && $amount !== '') {
                        $deductionType = \App\Models\ShipmentDeductionType::find($deduction_type_id);
                        
                        // Nếu là "Ghi chú", lưu vào column notes
                        if ($deductionType && $deductionType->name === 'Ghi chú') {
                            ShipmentDeduction::create([
                                'shipment_id' => $shipment->id,
                                'shipment_deduction_type_id' => (int)$deduction_type_id,
                                'amount' => 0, // Không lưu số tiền cho ghi chú
                                'notes' => (string)$amount, // Lưu ghi chú vào column notes
                            ]);
                        } else {
                            // Nếu là numeric, kiểm tra và lưu dưới dạng float
                            if (is_numeric($amount)) {
                                ShipmentDeduction::create([
                                    'shipment_id' => $shipment->id,
                                    'shipment_deduction_type_id' => (int)$deduction_type_id,
                                    'amount' => (float)$amount,
                                ]);
                            }
                        }
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
            
            // 4. Xóa và cập nhật lại các phụ cấp tài xế/lơ xe (chỉ khi không phải xe thuê)
            $shipment->shipmentDeductions()->whereNotNull('user_id')->delete();
            
            if (!empty($data['drivers']) && !($data['is_car_rental'] ?? false)) {
                foreach ($data['drivers'] as $person) {
                    // Kiểm tra user_id có tồn tại và là số nguyên dương
                    if (isset($person['user_id']) && is_numeric($person['user_id']) && (int)$person['user_id'] > 0) {
                        $user_id = (int)$person['user_id'];
                        
                        if (!empty($person['deductions'])) {
                            // Extract notes from deductions array if it exists
                            $notes = null;
                            if (isset($person['deductions']['notes'])) {
                                $notes = $person['deductions']['notes'];
                                unset($person['deductions']['notes']); // Remove notes from deductions array
                            }

                            $isMainDriver = false;
                            if (isset($person['deductions']['is_main_driver'])) {
                                $isMainDriver = (bool) $person['deductions']['is_main_driver'];
                                unset($person['deductions']['is_main_driver']); // Remove is_main_driver from deductions array
                            }
                            
                            foreach ($person['deductions'] as $deduction_type_id => $amount) {
                                // Kiểm tra deduction_type_id và amount có hợp lệ
                                if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0) {
                                    ShipmentDeduction::create([
                                        'user_id' => $user_id,
                                        'shipment_id' => $shipment->id,
                                        'shipment_deduction_type_id' => (int)$deduction_type_id ?? null,
                                        'amount' => (float)$amount ?? null,
                                        'notes' => $notes, // Add notes field
                                        'is_main_driver' => $isMainDriver
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            // 5. Xóa và cập nhật lại các phụ cấp tài xế phụ cấp (chỉ khi không phải xe thuê)
            if (!empty($data['driverPXs']) && !($data['is_car_rental'] ?? false)) {
                foreach ($data['driverPXs'] as $driverPX) {
                    // Kiểm tra user_id có tồn tại và là số nguyên dương
                    if (isset($driverPX['user_id']) && is_numeric($driverPX['user_id']) && (int)$driverPX['user_id'] > 0) {
                        $user_id = (int)$driverPX['user_id'];
                        
                        if (!empty($driverPX['deductions'])) {
                            // Extract notes from deductions array if it exists
                            $notes = null;
                            if (isset($driverPX['deductions']['notes'])) {
                                $notes = $driverPX['deductions']['notes'];
                                unset($driverPX['deductions']['notes']); // Remove notes from deductions array
                            }
                            
                            foreach ($driverPX['deductions'] as $deduction_type_id => $amount) {
                                // Kiểm tra deduction_type_id và amount có hợp lệ
                                if (is_numeric($deduction_type_id) && (int)$deduction_type_id > 0) {
                                    ShipmentDeduction::create([
                                        'user_id' => $user_id,
                                        'shipment_id' => $shipment->id,
                                        'shipment_deduction_type_id' => (int)$deduction_type_id ?? null,
                                        'amount' => (float)$amount ?? null,
                                        'is_main_driver' => false,
                                        'notes' => $notes, // Add notes field
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            $this->resetMonthlyReportFinalized($shipment->customer_id, $shipment->departure_time);
            
            return $shipment->refresh();
        });
    }

    public function delete(Shipment $shipment)
    {
        $customerId = $shipment->customer_id;
        $departureTime = $shipment->departure_time;
        $shipment->shipmentDeductions()->delete();
        $shipment->goods()->delete();
        $shipment->delete();
        $this->resetMonthlyReportFinalized($customerId, $departureTime);
    }

    /**
     * Reset trạng thái finalized của báo cáo tháng khi có thay đổi shipment
     */
    protected function resetMonthlyReportFinalized($customerId, $departureTime)
    {
        $month = date('Y-m', strtotime($departureTime));
        \App\Models\ShipmentReport::where('customer_id', $customerId)
            ->where('monthly', $month)
            ->update(['is_finalized' => false]);
    }
}
