<?php

namespace App\Http\Requests\CarRental;

use App\Http\Requests\Traits\UsesSystemDateFormat;
use Illuminate\Foundation\Http\FormRequest;

class StoreCarRentalRequest extends FormRequest
{
    use UsesSystemDateFormat;
    public function authorize()
    {
        return true;
    }

    /**
     * Summary of rules
     * @return array{cargo_description: string, customer_id: string, document_file: string, notes: string, pickup_datetime: string, valid_until: string}
     */
    public function rules()
    {
        return [
            'customer_id' => 'required',
            'status' => 'required',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
            'vehicles' => 'required|array',
            'vehicles.*.vehicle_id' => 'required',
            'vehicles.*.product_name' => 'required|string|max:255',
            'vehicles.*.unit' => 'required|in:tháng,ngày',
            'vehicles.*.amount' => 'required|integer|min:1',
            'vehicles.*.price' => 'required|numeric|min:0',
            'vehicles.*.money' => 'nullable|numeric|min:0',
            'vehicles.*.start_date' => 'required|date',
            'vehicles.*.end_date' => 'required|date',
            'vehicles.*.notes' => 'nullable',
        ];
    }

    /**
     * Summary of messages
     * @return array{customer_id.required: string, document_file.max: string, document_file.mimes: string, document_file.required: string, pickup_datetime.date: string, pickup_datetime.required: string, valid_until.date: string, valid_until.required: string}
     */
    public function messages()
    {
        return [
            'customer_id.required' => 'Khách hàng là bắt buộc',
            'status.required' => 'Trạng thái là bắt buộc',
            'valid_until.date' => 'Ngày hết hạn không đúng định dạng',
            'valid_until.after_or_equal' => 'Ngày hết hạn phải sau hoặc bằng ngày bắt đầu',
            'file.mimes' => 'File đính kèm phải có định dạng: pdf, doc, docx, xls, xlsx',
            'file.max' => 'File đính kèm không được vượt quá 10MB',
            'vehicles.*.vehicle_id.required' => 'Phương tiện là bắt buộc',
            'vehicles.*.unit.required' => 'Đơn vị là bắt buộc',
            'vehicles.*.amount.required' => 'Số lượng là bắt buộc',
            'vehicles.*.price.required' => 'Đơn giá là bắt buộc',
            'vehicles.*.start_date.required' => 'Ngày bắt đầu là bắt buộc',
            'vehicles.*.end_date.required' => 'Ngày kết thúc là bắt buộc',
            'vehicles.*.product_name.required' => 'Tên hàng là bắt buộc',
        ];
    }
}
