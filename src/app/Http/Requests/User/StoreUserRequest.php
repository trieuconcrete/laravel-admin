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
            'status' => ['required'],
            'address' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];

        // case license
        if ($this->has('license_type')) {
            return array_merge($common, [
                'license_type' => ['required', 'string'],
                'license_expire_date' => ['nullable', $this->getSystemDateFormatRule()],
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
            'phone.regex' => 'Số điện thoại không đúng định dạng',
            'phone.unique' => 'Số điện thoại đã tồn tại',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'name.required' => 'Vui lòng nhập họ tên nhân viên',
            'full_name.required' => 'Vui lòng nhập họ tên tài xế',
            'position.required' => 'Vui lòng chọn vị trí',
            'license_type.required' => 'Vui lòng chọn loại bằng lái',
            'status.required' => 'Vui lòng chọn trạng thái',
        ];
    }
}
