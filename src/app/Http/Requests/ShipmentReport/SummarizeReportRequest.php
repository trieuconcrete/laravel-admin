<?php

namespace App\Http\Requests\ShipmentReport;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ShipmentReport;

class SummarizeReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'statement_start_date' => 'required|date',
            'statement_end_date' => 'required|date|after_or_equal:statement_start_date',
            'shipment_type' => 'required|integer|in:1,2,3,4',
            'customer_id' => 'required|exists:customers,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $startDate = $this->input('statement_start_date');
            $endDate = $this->input('statement_end_date');
            $customerId = $this->input('customer_id');
            $shipmentType = $this->input('shipment_type');

            // Luôn kiểm tra overlap vì shipment_type là bắt buộc
            if (ShipmentReport::checkTimeOverlap($customerId, $startDate, $endDate, $shipmentType)) {
                $validator->errors()->add('time_overlap', 'Thời gian bảng kê đã chồng lên nhau với các bảng kê khác cùng loại.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'statement_start_date.required' => 'Ngày bắt đầu là bắt buộc.',
            'statement_end_date.required' => 'Ngày kết thúc là bắt buộc.',
            'statement_end_date.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
            'shipment_type.required' => 'Loại chuyến xe là bắt buộc.',
            'shipment_type.in' => 'Loại chuyến xe không hợp lệ.',
            'customer_id.required' => 'Khách hàng là bắt buộc.',
            'customer_id.exists' => 'Khách hàng không tồn tại.',
            'time_overlap' => 'Thời gian bảng kê đã chồng lên nhau với các bảng kê khác cùng loại.',
        ];
    }
} 