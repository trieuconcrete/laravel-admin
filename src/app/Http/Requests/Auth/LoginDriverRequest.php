<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginDriverRequest extends FormRequest
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
            // 'license_plate' => ['required', 'string'],
            'phone' => ['required', 'string', 'regex:/^(\+84[0-9]{8,9}|0[0-9]{9})$/'],
            'password' => [
                'required',
                'string',
                'min:6',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            // 'license_plate.required' => 'Biển số xe là bắt buộc.',
            // 'license_plate.string'   => 'Biển số xe phải là chuỗi ký tự.',

            'phone.required'  => 'Số điện thoại là bắt buộc.',
            'phone.string'    => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.regex'    => 'Số điện thoại không hợp lệ. Vui lòng nhập theo định dạng +84xxxxxxxx hoặc 0xxxxxxxxx.',

            'password.required'      => 'Mật khẩu là bắt buộc.',
            'password.string'        => 'Mật khẩu phải là chuỗi ký tự.',
            'password.min'           => 'Mật khẩu phải có ít nhất :min ký tự.',
        ];
    }
}
