<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Transaction;

class SearchTransactionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        if ($this->amount_min || $this->amount_max) {
            $this->merge([
                'amount_min' => str_replace(',', '', $this->amount_min),
                'amount_max' => str_replace(',', '', $this->amount_max),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'amount_min' => 'nullable|numeric|min:0',
            'amount_max' => 'nullable|numeric|min:0|gte:amount_min',
            'transaction_type' => 'nullable|string|in:' . implode(',', array_keys(Transaction::getTypes())),
            'payment_method' => 'nullable|string',
            'payment_status' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu',
            'amount_max.gte' => 'Số tiền tối đa phải lớn hơn hoặc bằng số tiền tối thiểu',
            'transaction_type.in' => 'Loại giao dịch không hợp lệ',
        ];
    }
}
