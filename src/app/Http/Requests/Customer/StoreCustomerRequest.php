<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Customer;

class StoreCustomerRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'min:2'
            ],
            'type' => [
                'required',
                Rule::in([Customer::TYPE_INDIVIDUAL, Customer::TYPE_BUSINESS])
            ],
            'phone' => [
                'required',
                'string',
                'regex:/^[0-9+\-\s()]+$/',
                'min:10',
                'max:15',
                Rule::unique('customers', 'phone')->whereNull('deleted_at')
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->whereNull('deleted_at')
            ],
            'address' => [
                'nullable',
                'string',
                'max:500'
            ],
            'province' => [
                'nullable',
                'string',
                'max:100'
            ],
            'district' => [
                'nullable',
                'string',
                'max:100'
            ],
            'ward' => [
                'nullable',
                'string',
                'max:100'
            ],
            'tax_code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('customers', 'tax_code')->whereNull('deleted_at')
            ],
            'establishment_date' => [
                'nullable',
                'date',
                'before_or_equal:today'
            ],
            'website' => [
                'nullable',
                'url',
                'max:255'
            ],
            'notes' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'is_active' => [
                'required',
                // 'boolean'
            ],
            
            // Thông tin người liên hệ chính (cho doanh nghiệp)
            'primary_contact_name' => [
                'nullable',
                'required_if:type,' . Customer::TYPE_BUSINESS,
                'string',
                'max:255'
            ],
            'primary_contact_position' => [
                'nullable',
                'string',
                'max:100'
            ],
            'primary_contact_phone' => [
                'nullable',
                'string',
                'regex:/^[0-9+\-\s()]+$/',
                'min:10',
                'max:15'
            ],
            'primary_contact_email' => [
                'nullable',
                'email',
                'max:255'
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên khách hàng là bắt buộc.',
            'name.min' => 'Tên khách hàng phải có ít nhất 2 ký tự.',
            'name.max' => 'Tên khách hàng không được vượt quá 255 ký tự.',
            
            'type.required' => 'Loại khách hàng là bắt buộc.',
            'type.in' => 'Loại khách hàng không hợp lệ.',
            
            'phone.required' => 'Số điện thoại là bắt buộc.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'phone.min' => 'Số điện thoại phải có ít nhất 10 số.',
            'phone.max' => 'Số điện thoại không được vượt quá 15 số.',
            'phone.unique' => 'Số điện thoại này đã được sử dụng.',
            
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            
            'tax_code.unique' => 'Mã số thuế này đã được sử dụng.',
            
            'establishment_date.date' => 'Ngày thành lập không đúng định dạng.',
            'establishment_date.before_or_equal' => 'Ngày thành lập không được là ngày trong tương lai.',
            
            'website.url' => 'Website phải là URL hợp lệ.',
            
            'is_active.required' => 'Trạng thái là bắt buộc.',
            'is_active.boolean' => 'Trạng thái không hợp lệ.',
            
            'primary_contact_name.required_if' => 'Tên người liên hệ chính là bắt buộc đối với doanh nghiệp.',
            'primary_contact_phone.regex' => 'Số điện thoại người liên hệ không đúng định dạng.',
            'primary_contact_email.email' => 'Email người liên hệ không đúng định dạng.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'tên khách hàng',
            'type' => 'loại khách hàng',
            'phone' => 'số điện thoại',
            'email' => 'email',
            'address' => 'địa chỉ',
            'tax_code' => 'mã số thuế',
            'establishment_date' => 'ngày thành lập',
            'website' => 'website',
            'notes' => 'ghi chú',
            'is_active' => 'trạng thái',
            'primary_contact_name' => 'tên người liên hệ chính',
            'primary_contact_position' => 'chức vụ người liên hệ',
            'primary_contact_phone' => 'số điện thoại người liên hệ',
            'primary_contact_email' => 'email người liên hệ',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validation tùy chỉnh: Nếu là doanh nghiệp thì cần có mã số thuế
            if ($this->input('type') === Customer::TYPE_BUSINESS) {
                if (empty($this->input('tax_code'))) {
                    $validator->errors()->add('tax_code', 'Mã số thuế là bắt buộc đối với doanh nghiệp.');
                }
            }
            
            // Validation: Kiểm tra định dạng mã số thuế (nếu có)
            if ($this->input('tax_code')) {
                $taxCode = $this->input('tax_code');
                // Mã số thuế doanh nghiệp: 10 hoặc 13 số
                if (!preg_match('/^[0-9]{10}$|^[0-9]{13}$/', $taxCode)) {
                    $validator->errors()->add('tax_code', 'Mã số thuế phải có 10 hoặc 13 chữ số.');
                }
            }
        });
    }
}
