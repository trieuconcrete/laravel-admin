<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Enum\UserStatus as EnumUserStatus;
use Illuminate\Validation\Rules\Enum;

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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Check if driver_license is present in request
        if ($this->has('driver_license')) {
            $rules = [
                'license_number' => 'required|string|max:100',
                'license_type'   => 'required|string|max:50',
                'issue_date'     => 'required|date',
                'expiry_date'    => 'required|date|after_or_equal:issue_date',
                'issued_by'      => 'required|string|max:100',
                'license_file'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                'license_status' => 'nullable',
            ];
        } else {
            $rules = [
                'full_name' => 'required|max:255',
                'email' => 'required|email|unique:users,email,' . $this->user->id,
                'username' => 'nullable|max:100|unique:users,username,' . $this->user->id,
                'birthday' => 'nullable|date',
                'phone' => 'nullable|string',
                'role' => 'required',
                'password' => ['nullable', 'confirmed', 'min:6'],
                'status' => 'required|boolean',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ];
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
