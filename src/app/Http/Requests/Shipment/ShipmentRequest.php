<?php

namespace App\Http\Requests\Shipment;

use App\Http\Requests\Traits\UsesSystemDateFormat;
use Illuminate\Foundation\Http\FormRequest;

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
        // Filter drivers array based on submitted rows
        if ($this->has('driver_row_indexes')) {
            $indexes = explode(',', $this->input('driver_row_indexes'));
            $drivers = $this->input('drivers', []);
            $filteredDrivers = [];
            
            foreach ($indexes as $index) {
                if (isset($drivers[$index])) {
                    $filteredDrivers[$index] = $drivers[$index];
                }
            }
            
            $this->merge([
                'drivers' => $filteredDrivers
            ]);
        }
        
        // Remove commas from deduction values
        if ($this->has('deductions')) {
            $deductions = $this->input('deductions', []);
            foreach ($deductions as $key => $value) {
                if (!empty($value)) {
                    $deductions[$key] = str_replace(',', '', $value);
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
                            $drivers[$driverIndex]['deductions'][$deductionKey] = str_replace(',', '', $value);
                        }
                    }
                }
            }
            $this->merge([
                'drivers' => $drivers
            ]);
        }

        // Remove commas from unit price
        if ($this->unit_price) {
            $this->merge([
                'unit_price' => str_replace(',', '', $this->unit_price),
            ]);
        }
    }

    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'origin' => 'required|string|max:255',
            'destination' => 'nullable|string|max:255',
            'departure_time' => 'required|' . $this->getSystemDateFormatRule(),
            'estimated_arrival_time' => 'required|' . $this->getSystemDateFormatRule() . '|after_or_equal:departure_time',
            'notes' => 'nullable|string',
            'status' => 'required|string',
            'distance' => 'nullable|numeric|min:0',
            'cargo_weight' => 'nullable|numeric|min:0',
            'trip_count' => 'nullable|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            // Chi phí chuyến hàng
            'deductions' => 'array',
            'deductions.*' => 'nullable|numeric|min:0',
            // Hàng hóa
            'goods' => 'array',
            'goods.*.name' => 'required|string|max:255',
            'goods.*.quantity' => 'nullable|integer|min:0',
            'goods.*.unit' => 'required|numeric|min:0',
            'goods.*.notes' => 'nullable|string|max:255',
            'goods.*.weight' => 'nullable|numeric|min:0',
            // Tài xế/lơ xe và phụ cấp
            'drivers' => 'array',
            'drivers.*.user_id' => 'required|exists:users,id',
            'drivers.*.deductions' => 'array',
            'drivers.*.deductions.*' => 'nullable|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'customer_id' => 'Khách hàng',
            'vehicle_id' => 'Phương tiện',
            'origin' => 'Điểm xuất phát',
            'destination' => 'Điểm đến',
            'departure_time' => 'Thời gian khởi hành',
            'estimated_arrival_time' => 'Thời gian dự kiến đến',
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
        ];
    }
}
