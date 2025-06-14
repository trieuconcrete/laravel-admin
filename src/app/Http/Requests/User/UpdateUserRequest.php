<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Http\Requests\Traits\UsesSystemDateFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Enum\UserStatus as EnumUserStatus;
use Illuminate\Validation\Rules\Enum;
use App\Constants;

class UpdateUserRequest extends FormRequest
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
        switch ($this->user_action) {
            case Constants::USER_ACTION_CHANGE_INFORMATION:
                $rules = [
                    'full_name' => 'required|max:255',
                    'phone' => [
                        'required',
                        'string',
                        'regex:/^(0|\+84|84)(3[2-9]|5[6|8|9]|7[0|6-9]|8[1-5]|9[0-9])[0-9]{7}$/',
                        Rule::unique('users', 'phone')->whereNull('deleted_at')->ignore($this->user->id),
                    ],
                    'email' => [
                        'required',
                        'email',
                        Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($this->user->id),
                    ],
                    'birthday' => 'nullable|' . $this->getSystemDateFormatRule(),
                    'password' => ['nullable', 'confirmed', 'min:6'],
                    'status' => 'required|boolean',
                    'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                    'notes' => ['nullable', 'string'],
                    'id_number' => 'required|max:20',
                    'salary_base' => ['nullable', 'numeric', 'min:0'],
                    'address' => 'nullable|max:100',
                    'tab' => 'nullable|string',
                    'join_date' => 'required|' . $this->getSystemDateFormatRule(),
                ];
                break;
            case Constants::USER_ACTION_CHANGE_LICENSE:
                $rules = [
                    'license_number' => 'required|string|max:100',
                    'license_type'   => 'required|string|max:50',
                    'issue_date'     => 'nullable|' . $this->getSystemDateFormatRule(),
                    'expiry_date'    => 'nullable|' . $this->getSystemDateFormatRule() . '|after_or_equal:issue_date',
                    'issued_by'      => 'nullable|string|max:100',
                    'license_file'   => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
                    'license_status' => 'nullable',
                    'tab' => 'nullable|string',
                    'address' => 'nullable|max:100',
                ];
                break;
            case Constants::USER_ACTION_CHANGE_PASSWORD:
                $rules = [
                    'password' => 'required|string|min:8|confirmed',
                    'tab' => 'nullable|string',
                ];
                break;
            default:
                $rules = [];
                break;
        }
    
        return $rules;
    }

    /**
     * Summary of messages
     * @return array{email.email: string, email.unique: string, full_name.required: string, license_type.required: string, name.required: string, phone.regex: string, phone.required: string, phone.unique: string, position.required: string, status.required: string}
     */
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

    /**
     * Summary of failedValidation
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    protected function failedValidation(Validator $validator)
    {
        // Store the uploaded avatar in session if there's a validation error
        if ($this->hasFile('avatar')) {
            $avatarFile = $this->file('avatar');
            $avatarTemp = $avatarFile->get();
            $avatarBase64 = 'data:' . $avatarFile->getMimeType() . ';base64,' . base64_encode($avatarTemp);
            session()->flash('_avatar_temp', $avatarBase64);
        }

        if ($this->hasFile('license_file')) {
            $licenseFile = $this->file('license_file');
            $licenseTemp = $licenseFile->get();
            $licenseBase64 = 'data:' . $licenseFile->getMimeType() . ';base64,' . base64_encode($licenseTemp);
            session()->flash('_license_file_temp', $licenseBase64);
        }
        
        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
                ->with('active_tab', $this->input('tab'))
        );
    }
}
