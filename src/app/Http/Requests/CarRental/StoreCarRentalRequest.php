<?php

namespace App\Http\Requests\CarRental;

use App\Helpers\NumberHelper;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\UsesSystemDateFormat;

class StoreCarRentalRequest extends FormRequest
{
    use UsesSystemDateFormat;
    public function authorize()
    {
        return true;
    }

    /**
     * Summary of prepareForValidation
     * @return void
     */
    public function prepareForValidation()
    {
        $this->merge([
            'monthly_rental_fee' => NumberHelper::parseNumber($this->monthly_rental_fee),
            'overtime_fee_per_hour' => NumberHelper::parseNumber($this->overtime_fee_per_hour),
            'max_distance' => NumberHelper::parseNumber($this->max_distance),
            'over_distance_fee_per_km' => NumberHelper::parseNumber($this->over_distance_fee_per_km),
        ]);
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
            'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            'monthly_rental_fee' => 'required|numeric|min:0|max:1000000000'
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
