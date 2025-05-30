<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShipmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
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
            'distance' => 'nullable|integer|min:1',
            // Chi phí chuyến hàng
            'deductions' => 'array',
            'deductions.*' => 'nullable|numeric|min:0',
            // Hàng hóa
            'goods' => 'array',
            'goods.*.name' => 'required|string|max:255',
            'goods.*.quantity' => 'required|integer|min:1',
            'goods.*.unit' => 'required|string|max:50',
            'goods.*.notes' => 'nullable|string|max:255',
            'goods.*.weight' => 'nullable|integer|min:1',
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
