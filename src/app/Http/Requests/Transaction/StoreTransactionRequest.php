<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Payment;

class StoreTransactionRequest extends FormRequest
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

        /**
     * Summary of prepareForValidation
     * @return void
     */
    public function prepareForValidation()
    {
        if ($this->amount) {
            $this->merge([
                'amount' => str_replace(',', '', $this->amount),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:' . implode(',', array_keys(Payment::getPaymentMethods())),
            'notes' => 'nullable|string|max:500',
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
            'amount.required' => 'Vui lòng nhập số tiền',
            'amount.numeric' => 'Số tiền phải là số',
            'amount.min' => 'Số tiền phải lớn hơn hoặc bằng 0',
            'payment_date.required' => 'Vui lòng chọn ngày thanh toán',
            'payment_date.date' => 'Ngày thanh toán không hợp lệ',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ',
            'notes.max' => 'Chú thích không được vượt quá 500 ký tự',
        ];
    }
}
