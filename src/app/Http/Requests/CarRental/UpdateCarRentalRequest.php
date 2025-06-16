<?php

namespace App\Http\Requests\CarRental;

use App\Http\Requests\Traits\UsesSystemDateFormat;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCarRentalRequest extends FormRequest
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
            // 'pickup_datetime' => 'required|' . $this->getSystemDateFormatRule(),
            // 'valid_until' => 'nullable|after_or_equal:pickup_datetime|' . $this->getSystemDateFormatRule(),
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            // 'file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:10240'
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
            'pickup_datetime.required' => 'Ngày bắt đầu là bắt buộc',
            'pickup_datetime.date' => 'Ngày bắt đầu không đúng định dạng',
            'valid_until.required' => 'Ngày hết hạn là bắt buộc',
            'valid_until.date' => 'Ngày hết hạn không đúng định dạng',
            'valid_until.after_or_equal' => 'Ngày hết hạn phải sau hoặc bằng ngày bắt đầu',
            'file.mimes' => 'File đính kèm phải có định dạng: pdf, doc, docx, xls, xlsx',
            'file.required' => 'File đính kèm là băt buộc',
            'file.max' => 'File đính kèm không được vượt quá 10MB'
        ];
    }
}
