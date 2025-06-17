<?php

namespace App\Http\Requests\SalaryAdvanceRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SalaryAdvanceRequest;

class StoreSalaryAdvanceRequestRequest extends FormRequest
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
                'amount' => str_replace('.', '', $this->amount),
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
            'amount' => 'required|numeric|min:1|max:999999999',
            'status' => 'required|string|in:' . implode(',', array_keys(SalaryAdvanceRequest::getStatuses())),
            'reason' => 'nullable|string|max:500',
            'user_id' => 'required|exists:users,id',
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
            'amount.required' => 'Vui lòng nhập số tiền ứng',
            'amount.numeric' => 'Số tiền ứng phải là số',
            'amount.min' => 'Số tiền ứng phải lớn hơn 0',
            'status.required' => 'Vui lòng chọn trạng thái',
            'status.in' => 'Trạng thái không hợp lệ',
            'user_id.required' => 'Vui lòng chọn người dùng',
            'user_id.exists' => 'Người dùng không tồn tại',
        ];
    }
}
