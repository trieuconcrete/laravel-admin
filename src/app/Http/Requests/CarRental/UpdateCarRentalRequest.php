<?php

namespace App\Http\Requests\CarRental;

use App\Helpers\NumberHelper;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Traits\UsesSystemDateFormat;

class UpdateCarRentalRequest extends FormRequest
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
            'monthly_rental_fee' => str_replace(',', '', $this->monthly_rental_fee),
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
            'monthly_rental_fee' => 'required|numeric|min:0|max:1000000000',
            'overtime_fee_per_hour' => 'nullable|numeric|min:0|max:1000000',
            'max_distance' => 'nullable|numeric|min:0|max:100000',
            'over_distance_fee_per_km' => 'nullable|numeric|min:0|max:100000',
            'invoice_number' => 'nullable|string|max:255',
            'statement_number' => 'nullable|string|max:255',
            'currency' => 'nullable|string|max:10'
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
            'pickup_datetime.date' => 'Ngày bắt đầu không đúng định dạng',
            'valid_until.date' => 'Ngày hết hạn không đúng định dạng',
            'valid_until.after_or_equal' => 'Ngày hết hạn phải sau hoặc bằng ngày bắt đầu',
            'file.mimes' => 'File đính kèm phải có định dạng: pdf, doc, docx, xls, xlsx',
            'file.max' => 'File đính kèm không được vượt quá 10MB',
            'monthly_rental_fee.numeric' => 'Phí thuê xe phải là số',
            'monthly_rental_fee.min' => 'Phí thuê xe không được âm',
            'monthly_rental_fee.max' => 'Phí thuê xe không được vượt quá 1 tỷ',
            'overtime_fee_per_hour.numeric' => 'Phí tăng ca/giờ phải là số',
            'overtime_fee_per_hour.min' => 'Phí tăng ca/giờ không được âm',
            'overtime_fee_per_hour.max' => 'Phí tăng ca/giờ không được vượt quá 1 triệu',
            'max_distance.numeric' => 'Số km tối đa phải là số',
            'max_distance.min' => 'Số km tối đa không được âm',
            'max_distance.max' => 'Số km tối đa không được vượt quá 100,000',
            'over_distance_fee_per_km.numeric' => 'Phí theo km chạy vượt phải là số',
            'over_distance_fee_per_km.min' => 'Phí theo km chạy vượt không được âm',
            'over_distance_fee_per_km.max' => 'Phí theo km chạy vượt không được vượt quá 100,000'
        ];
    }
}
