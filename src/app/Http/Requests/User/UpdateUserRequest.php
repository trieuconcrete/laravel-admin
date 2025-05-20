<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Enum\UserStatus as EnumUserStatus;
use Illuminate\Validation\Rules\Enum;
use App\Constants;

class UpdateUserRequest extends FormRequest
{
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
        switch ($this->user_action) {
            case Constants::USER_ACTION_CHANGE_INFORMATION:
                $rules = [
                    'full_name' => 'required|max:255',
                    'phone' => [
                        'required',
                        'string',
                        'regex:/^(0|\+84|84)(3[2-9]|5[6|8|9]|7[0|6-9]|8[1-5]|9[0-9])[0-9]{7}$/',
                        'unique:users,phone,' . $this->user->id,
                    ],
                    'email' => 'required|email|unique:users,email,' . $this->user->id,
                    'birthday' => 'nullable|date',
                    'password' => ['nullable', 'confirmed', 'min:6'],
                    'status' => 'required|boolean',
                    'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                    'notes' => ['nullable', 'string'],
                    'id_number' => 'required|max:20',
                    'salary_base' => ['required', 'numeric', 'min:0'],
                    'address' => 'nullable|max:100',
                ];
                break;
            case Constants::USER_ACTION_CHANGE_LICENSE:
                $rules = [
                    'license_number' => 'required|string|max:100',
                    'license_type'   => 'required|string|max:50',
                    'issue_date'     => 'required|date',
                    'expiry_date'    => 'required|date|after_or_equal:issue_date',
                    'issued_by'      => 'required|string|max:100',
                    'license_file'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                    'license_status' => 'nullable',
                ];
                break;
            case Constants::USER_ACTION_CHANGE_PASSWORD:
                $rules = [
                    'password' => 'required|string|min:8|confirmed',
                ];
                break;
            default:
                $rules = [];
                break;
        }
    
        return $rules;
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
