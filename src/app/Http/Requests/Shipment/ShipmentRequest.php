<?php

namespace App\Http\Requests\Shipment;

use App\Http\Requests\Traits\UsesSystemDateFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class ShipmentRequest extends FormRequest
{
    use UsesSystemDateFormat;
    public function authorize()
    {
        return true;
    }
    
    /**
     * Prepare the data for validation.
     * This method will filter drivers array to only include rows that were actually submitted.
     */
    protected function prepareForValidation()
    {
        // Xử lý vehicle_id để đảm bảo là integer
        if ($this->has('vehicle_id') && $this->input('vehicle_id')) {
            $vehicleId = $this->input('vehicle_id');
            // Nếu là string chứa text, cố gắng extract ID
            if (is_string($vehicleId) && strpos($vehicleId, '-') !== false) {
                // Nếu có format "92-G106594 - Xe đầu kéo", extract số đầu tiên
                if (preg_match('/^(\d+)/', $vehicleId, $matches)) {
                    $this->merge(['vehicle_id' => (int)$matches[1]]);
                }
            } else {
                // Đảm bảo là integer
                $this->merge(['vehicle_id' => (int)$vehicleId]);
            }
        }
        
        // Xử lý format thời gian start_time và end_time
        if ($this->has('start_time') && $this->input('start_time')) {
            $startTime = $this->input('start_time');
            // Đảm bảo format H:i
            if (preg_match('/^\d{1,2}:\d{2}$/', $startTime)) {
                // Format đã đúng, không cần thay đổi
            } else {
                // Nếu format không đúng, cố gắng parse và format lại
                $time = strtotime($startTime);
                if ($time !== false) {
                    $this->merge(['start_time' => date('H:i', $time)]);
                }
            }
        }
        
        if ($this->has('end_time') && $this->input('end_time')) {
            $endTime = $this->input('end_time');
            // Đảm bảo format H:i
            if (preg_match('/^\d{1,2}:\d{2}$/', $endTime)) {
                // Format đã đúng, không cần thay đổi
            } else {
                // Nếu format không đúng, cố gắng parse và format lại
                $time = strtotime($endTime);
                if ($time !== false) {
                    $this->merge(['end_time' => date('H:i', $time)]);
                }
            }
        }
        
        // Xử lý is_car_rental để đảm bảo không null
        if ($this->has('is_car_rental')) {
            $isCarRental = $this->input('is_car_rental');
            if ($isCarRental === null || $isCarRental === '') {
                $this->merge(['is_car_rental' => false]);
            } else {
                $this->merge(['is_car_rental' => (bool)$isCarRental]);
            }
        } else {
            // Nếu không có field is_car_rental, set default là false
            $this->merge(['is_car_rental' => false]);
        }
        
        // TEMPORARY: Disable filtering to test if this is the issue
        // Filter drivers array based on submitted rows
        if ($this->has('driver_row_indexes')) {
            $indexes = explode(',', $this->input('driver_row_indexes'));
            $drivers = $this->input('drivers', []);
            $filteredDrivers = [];
            
            // Debug logging
            if (app()->environment('local')) {
                Log::info('ShipmentRequest - prepareForValidation:', [
                    'driver_row_indexes' => $this->input('driver_row_indexes'),
                    'indexes_array' => $indexes,
                    'original_drivers' => $drivers,
                    'drivers_keys' => array_keys($drivers)
                ]);
            }
            
            foreach ($indexes as $index) {
                $cleanIndex = trim($index);
                if (isset($drivers[$cleanIndex])) {
                    $filteredDrivers[$cleanIndex] = $drivers[$cleanIndex];
                }
            }
            
            // Debug logging
            if (app()->environment('local')) {
                Log::info('ShipmentRequest - after filtering:', [
                    'filtered_drivers' => $filteredDrivers,
                    'original_count' => count($drivers),
                    'filtered_count' => count($filteredDrivers)
                ]);
            }
            
            // Re-enable filtering for proper testing
            $this->merge([
                'drivers' => $filteredDrivers
            ]);
        }
        
        // Remove commas from deduction values (except for "Ghi chú")
        if ($this->has('deductions')) {
            $deductions = $this->input('deductions', []);
            $deductionTypes = \App\Models\ShipmentDeductionType::where('status', 'active')->get()->keyBy('id');
            
            foreach ($deductions as $key => $value) {
                if (!empty($value)) {
                    $deductionType = $deductionTypes->get($key);
                    // Chỉ xóa dấu phẩy nếu không phải là "Ghi chú"
                    if ($deductionType && $deductionType->name !== 'Ghi chú') {
                        $deductions[$key] = str_replace(',', '', $value);
                    }
                }
            }
            $this->merge([
                'deductions' => $deductions
            ]);
        }
        
        // Remove commas from goods unit values
        if ($this->has('goods')) {
            $goods = $this->input('goods', []);
            foreach ($goods as $key => $item) {
                if (isset($item['unit']) && !empty($item['unit'])) {
                    $goods[$key]['unit'] = str_replace(',', '', $item['unit']);
                }
                if (isset($item['amount']) && !empty($item['amount'])) {
                    $goods[$key]['amount'] = str_replace(',', '', $item['amount']);
                }
            }
            $this->merge([
                'goods' => $goods
            ]);
        }
        
        // Remove commas from driver deduction values
        if ($this->has('drivers')) {
            $drivers = $this->input('drivers', []);
            foreach ($drivers as $driverIndex => $driver) {
                if (isset($driver['deductions']) && is_array($driver['deductions'])) {
                    foreach ($driver['deductions'] as $deductionKey => $value) {
                        if (!empty($value)) {
                            // Chỉ xóa dấu phẩy nếu không phải là "Ghi chú"
                            if ($deductionKey !== 'Ghi chú') {
                            $drivers[$driverIndex]['deductions'][$deductionKey] = str_replace(',', '', $value);
                            }
                        }
                    }
                }
            }
            $this->merge([
                'drivers' => $drivers
            ]);
        }

        // Remove commas from driverPX deduction values
        if ($this->has('driverPXs')) {
            $driverPXs = $this->input('driverPXs', []);
            foreach ($driverPXs as $driverIndex => $driver) {
                if (isset($driver['deductions']) && is_array($driver['deductions'])) {
                    foreach ($driver['deductions'] as $deductionKey => $value) {
                        if (!empty($value)) {
                            // Chỉ xóa dấu phẩy nếu không phải là "Ghi chú"
                            if ($deductionKey !== 'Ghi chú') {
                            $driverPXs[$driverIndex]['deductions'][$deductionKey] = str_replace(',', '', $value);
                            }
                        }
                    }
                }
            }
            $this->merge([
                'driverPXs' => $driverPXs
            ]);
        }

        // Remove commas from unit_price
        if ($this->unit_price) {
            $this->merge([
                'unit_price' => str_replace(',', '', $this->unit_price),
            ]);
        }
        // Remove commas from unit_price_for_car_rental
        if ($this->unit_price_for_car_rental) {
            $this->merge([
                'unit_price_for_car_rental' => str_replace(',', '', $this->unit_price_for_car_rental),
            ]);
        }
        // Remove commas from unit_price_for_driver
        if ($this->unit_price_for_driver) {
            $this->merge([
                'unit_price_for_driver' => str_replace(',', '', $this->unit_price_for_driver),
            ]);
        }
        // Remove commas from cargo_weight
        if ($this->cargo_weight) {
            $this->merge([
                'cargo_weight' => str_replace(',', '', $this->cargo_weight),
            ]);
        }
    }

    public function rules()
    {
        // Debug logging
        if (app()->environment('local')) {
            // Kiểm tra xem vehicle_id có tồn tại trong database không
            $vehicleId = $this->input('vehicle_id');
            $vehicleExists = null;
            if ($vehicleId) {
                $vehicleExists = \App\Models\Vehicle::where('vehicle_id', $vehicleId)->exists();
            }
            
            Log::info('ShipmentRequest - rules:', [
                'all_data' => $this->all(),
                'is_car_rental' => $this->input('is_car_rental'),
                'is_car_rental_after_processing' => $this->input('is_car_rental'),
                'vehicle_id' => $this->input('vehicle_id'),
                'vehicle_id_type' => gettype($this->input('vehicle_id')),
                'vehicle_id_raw' => $this->input('vehicle_id'),
                'vehicle_exists_in_db' => $vehicleExists,
                'available_vehicle_ids' => \App\Models\Vehicle::pluck('vehicle_id')->toArray(),
                'shipment_type' => $this->input('shipment_type'),
                'start_time' => $this->input('start_time'),
                'end_time' => $this->input('end_time'),
            ]);
        }
        
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'nullable|exists:vehicles,vehicle_id',
            'origin' => 'required|string|max:255',
            'destination' => 'nullable|string|max:255',
            'origin2' => 'nullable|string|max:255',
            'destination2' => 'nullable|string|max:255',
            'origin3' => 'nullable|string|max:255',
            'destination3' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'company2' => 'nullable|string|max:255',
            'company3' => 'nullable|string|max:255',
            'address_destination' => 'nullable|string|max:255',
            'address_destination2' => 'nullable|string|max:255',
            'address_destination3' => 'nullable|string|max:255',
            'departure_time' => 'required|' . $this->getSystemDateFormatRule(),
            'estimated_arrival_time' => 'nullable|' . $this->getSystemDateFormatRule() . '|after_or_equal:departure_time',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'run_date' => 'nullable|date',
            'shipment_type' => 'required|integer|in:1,2,3,4',
            'is_car_rental' => 'nullable|boolean',
            'notes' => 'nullable|string',
            'status' => 'required|string',
            'distance' => 'nullable|numeric|min:0',
            'cargo_weight' => 'nullable|numeric|min:0',
            'trip_count' => 'nullable|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'overtime_rate' => 'nullable|numeric|min:0',
            'is_overtime_at_noon' => 'nullable|boolean',
            'unit_price_for_car_rental' => 'nullable|numeric|min:0',
            'unit_price_for_driver' => 'nullable|numeric|min:0',
            // Chi phí chuyến hàng
            'deductions' => 'array',
            'deductions.*' => 'nullable', // Cho phép cả numeric và string cho "Ghi chú"
            // Hàng hóa
            'goods' => 'array',
            'goods.*.name' => 'nullable|string|max:255',
            'goods.*.quantity' => 'nullable|integer|min:0',
            'goods.*.unit' => 'nullable|numeric|min:0',
            'goods.*.notes' => 'nullable|string|max:255',
            'goods.*.weight' => 'nullable|numeric|min:0',
            'goods.*.amount' => 'nullable|numeric|min:0',
        ];

        // Nếu không phải xe thuê, thì yêu cầu thông tin tài xế và phương tiện
        if (!$this->input('is_car_rental')) {
            $rules['vehicle_id'] = 'required|exists:vehicles,vehicle_id';
            $rules['drivers'] = 'array';
            $rules['drivers.*.user_id'] = 'required|exists:users,id';
            $rules['drivers.*.deductions'] = 'array';
            $rules['drivers.*.deductions.*'] = 'nullable';
            
            $rules['driverPXs'] = 'array|nullable';
            $rules['driverPXs.*.user_id'] = 'nullable|exists:users,id';
            $rules['driverPXs.*.deductions'] = 'array';
            $rules['driverPXs.*.deductions.*'] = 'nullable';
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'customer_id' => 'Khách hàng',
            'vehicle_id' => 'Phương tiện',
            'origin' => 'Điểm xuất phát',
            'destination' => 'Điểm đến',
            'origin2' => 'Điểm đi 2',
            'destination2' => 'Điểm đến 2',
            'origin3' => 'Điểm đi 3',
            'destination3' => 'Điểm đến 3',
            'company' => 'Công ty',
            'company2' => 'Công ty 2',
            'company3' => 'Công ty 3',
            'address_destination' => 'Địa chỉ đến công ty',
            'address_destination2' => 'Địa chỉ đến công ty 2',
            'address_destination3' => 'Địa chỉ đến công ty 3',
            'departure_time' => 'Thời gian khởi hành',
            'estimated_arrival_time' => 'Thời gian dự kiến đến',
            'start_time' => 'Giờ khởi hành',
            'end_time' => 'Giờ đến',
            'run_date' => 'Ngày chạy',
            'shipment_type' => 'Loại chuyến xe',
            'overtime_rate' => 'Đơn giá tăng ca',
            'is_overtime_at_noon' => 'Tăng ca trưa',
            'is_car_rental' => 'Xe HPL Thuê',
            'notes' => 'Ghi chú',
            'status' => 'Trạng thái',
            'deductions' => 'Chi phí chuyến hàng',
            'deductions.*' => 'Số tiền chi phí',
            'goods' => 'Danh sách hàng hóa',
            'goods.*.name' => 'Tên hàng hóa',
            'goods.*.quantity' => 'Số lượng hàng hóa',
            'goods.*.unit' => 'Đơn vị hàng hóa',
            'drivers' => 'Danh sách tài xế/lơ xe',
            'drivers.*.user_id' => 'Nhân sự',
            'drivers.*.deductions.*' => 'Số tiền phụ cấp',
            // Tài xế phụ cấp
            'driverPXs' => 'Danh sách tài xế phụ cấp',
            'driverPXs.*.user_id' => 'Nhân sự',
            'driverPXs.*.deductions.*' => 'Số tiền phụ cấp',
        ];
    }
}
