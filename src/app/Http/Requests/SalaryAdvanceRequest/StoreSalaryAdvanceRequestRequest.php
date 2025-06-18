<?php

namespace App\Http\Requests\SalaryAdvanceRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SalaryAdvanceRequest;
use App\Http\Requests\Traits\UsesSystemDateFormat;

class StoreSalaryAdvanceRequestRequest extends FormRequest
{
    use UsesSystemDateFormat;

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
     *`
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:1|max:999999999',
            'type' => 'required|string|in:' . implode(',', array_keys(SalaryAdvanceRequest::getTypes())),
            'status' => 'required|string|in:' . implode(',', array_keys(SalaryAdvanceRequest::getStatuses())),
            'advance_month' => ['required', $this->getSystemDateFormatRule()],
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
            'amount.required' => 'Vui lòng nhập số tiền',
            'amount.numeric' => 'Số tiền phải là số',
            'amount.min' => 'Số tiền phải lớn hơn 0',
            'status.required' => 'Vui lòng chọn trạng thái',
            'status.in' => 'Trạng thái không hợp lệ',
            'advance_month.required' => 'Vui lòng chọn tháng yêu cầu',
            'user_id.required' => 'Vui lòng chọn người dùng',
            'user_id.exists' => 'Người dùng không tồn tại',
            'type.required' => 'Vui lòng chọn loại yêu cầu',
            'type.in' => 'Loại yêu cầu không hợp lệ',
        ];
    }
}
