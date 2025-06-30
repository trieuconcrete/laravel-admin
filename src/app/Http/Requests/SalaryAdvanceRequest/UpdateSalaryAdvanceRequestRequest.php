<?php

namespace App\Http\Requests\SalaryAdvanceRequest;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\SalaryAdvanceRequest;
use App\Http\Requests\Traits\UsesSystemDateFormat;

class UpdateSalaryAdvanceRequestRequest extends FormRequest
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
        // Format amount by removing thousand separators
        if ($this->amount) {
            $this->merge([
                'amount' => str_replace('.', '', $this->amount),
            ]);
        }

        // Convert date format from d/m/Y to Y-m-d
        if ($this->advance_month) {
            $date = \DateTime::createFromFormat('d/m/Y', $this->advance_month);
            if ($date) {
                $this->merge([
                    'advance_month' => $date->format('Y-m-d')
                ]);
            }
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
            'advance_month' => [
                'required',
                // 'date_format:d/m/Y',
                // function ($attribute, $value, $fail) {
                //     $date = \DateTime::createFromFormat('d/m/Y', $value);
                //     if (!$date || $date->format('d/m/Y') !== $value) {
                //         $fail('Định dạng ngày tháng không hợp lệ. Vui lòng sử dụng định dạng dd/mm/yyyy.');
                //     }
                // },
            ],
            'reason' => 'nullable|string|max:500',
            // 'user_id' => 'required|exists:users,id',
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
