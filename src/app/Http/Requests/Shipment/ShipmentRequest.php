<?php

namespace App\Http\Requests\Shipment;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentRequest extends FormRequest
{
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
    }

    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'vehicle_id' => 'required|exists:vehicles,vehicle_id',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_time' => 'required|date',
            'estimated_arrival_time' => 'required|date|after_or_equal:departure_time',
            'notes' => 'nullable|string',
            'status' => 'required|string',
            'distance' => 'nullable|numeric|min:0',
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
