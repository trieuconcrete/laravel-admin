<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Traits\UsesSystemDateFormat;
use Illuminate\Foundation\Http\FormRequest;
use App\Enum\UserStatus as EnumUserStatus;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    use UsesSystemDateFormat;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Summary of prepareForValidation
     * @return void
     */
    public function prepareForValidation()
    {
        if ($this->salary_base) {
            $this->merge([
                'salary_base' => str_replace(',', '', $this->salary_base),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $common = [
            'full_name' => ['required', 'string', 'max:100'],
            'phone' => [
                'required',
                'string',
                'regex:/^(0|\+84|84)(3[2-9]|5[6|8|9]|7[0|6-9]|8[1-5]|9[0-9])[0-9]{7}$/',
                Rule::unique('users', 'phone')->whereNull('deleted_at')
            ],
            'id_number' => ['required', 'max:20'],
            'email' => [
                'nullable', 
                'email',
                Rule::unique('users', 'email')->whereNull('deleted_at')
            ],
            'birthday' => ['nullable', $this->getSystemDateFormatRule()],
            'join_date' => ['nullable', $this->getSystemDateFormatRule()],
            'salary_base' => ['nullable', 'numeric'],
            'salary_type' => ['nullable', 'integer', 'in:1,2'],
            'salary_by_percent' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'status' => ['required'],
            'gender' => 'nullable',
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'has_insurance' => ['nullable', 'boolean'],
            'insurance_start_date' => ['nullable', $this->getSystemDateFormatRule()],
            'social_insurance_amount' => ['nullable', 'numeric', 'min:0'],
            'social_insurance_number' => ['nullable', 'string', 'max:20'],
        ];

        // case license
        if ($this->has('license_type')) {
            return array_merge($common, [
                'license_number' => ['nullable', 'string', 'max:50'],
                'license_type' => ['nullable', 'string'],
                'issue_date' => ['nullable', $this->getSystemDateFormatRule()],
                'license_expire_date' => ['nullable', $this->getSystemDateFormatRule()],
                'issued_by' => ['nullable', 'string', 'max:255'],
                'license_status' => ['nullable', 'string'],
                'license_file' => ['nullable', 'file', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            ]);
        }

        // case employee
        return array_merge($common, [
            'position_id' => ['required', 'string'],
        ]);
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Vui lòng nhập số điện thoại',
            'insurance_start_date.required_if' => 'Vui lòng nhập ngày bắt đầu đóng bảo hiểm khi có đóng bảo hiểm',
            'phone.regex' => 'Số điện thoại không đúng định dạng',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'name.required' => 'Vui lòng nhập họ tên nhân viên',
            'full_name.required' => 'Vui lòng nhập họ tên tài xế',
            'position.required' => 'Vui lòng chọn vị trí',
            'license_type.required' => 'Vui lòng chọn loại bằng lái',
            'license_number.required' => 'Vui lòng nhập số bằng lái',
            'license_file.mimes' => 'File bằng lái phải có định dạng: jpeg, png, jpg, gif',
            'license_file.max' => 'File bằng lái không được vượt quá 2MB',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
