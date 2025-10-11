<?php

namespace App\Http\Requests\Vehicle;

use App\Http\Requests\Traits\UsesSystemDateFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateVehicleRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'plate_number' => [
                'required',
                'string',
                Rule::unique('vehicles', 'plate_number')->ignore($this->route('vehicle')->vehicle_id, 'vehicle_id'),
            ],
            'vehicle_type_id' => 'required|exists:vehicle_types,vehicle_type_id',
            'driver_id' => 'nullable|exists:users,id',
            'capacity' => 'nullable|numeric|min:0',
            'manufactured_year' => 'nullable|integer|min:1990|max:' . date('Y'),
            'status' => 'required|in:active,maintenance,inactive',
            'documents' => 'array',
            'documents.*.document_type' => 'nullable|string|in:' . implode(',', array_keys(\App\Models\VehicleDocument::getDocumentTypes())),
            'documents.*.issue_date' => 'nullable|',
            'documents.*.expiry_date' => 'nullable|after_or_equal:documents.*.issue_date',
            'documents.*.document_number' => 'nullable|string',
            'documents.*.document_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
            'is_car_rental' => 'boolean',
        ];

        // Conditional validation for car rental vehicles
        if ($this->input('is_car_rental')) {
            $rules['customer_id'] = 'nullable|exists:customers,id';
            $rules['customer_name'] = 'required_without:customer_id|string|max:255';
            $rules['customer_phone'] = 'required_without:customer_id|string|max:20';
            // $rules['customer_email'] = 'required_without:customer_id|email|max:255';
            $rules['customer_address'] = 'nullable|string|max:500';
        }

        return $rules;
    }

    /**
     * Summary of messages
     * @return array{documents.*.document_file.max: string, documents.*.document_file.mimes: string, documents.*.document_file.required: string}
     */
    public function messages()
    {
        return [
            'documents.*.document_file.required' => 'File không được để trống',
            'documents.*.document_file.mimes' => 'File phải là pdf, jpg hoặc png',
            'documents.*.document_file.max' => 'File vượt quá dung lượng cho phép',
            'customer_name.required_without' => 'Tên khách hàng là bắt buộc khi không chọn khách hàng có sẵn',
            'customer_phone.required_without' => 'Số điện thoại khách hàng là bắt buộc khi không chọn khách hàng có sẵn',
            'customer_phone.max' => 'Số điện thoại không được vượt quá 20 ký tự',
            'customer_email.required_without' => 'Email khách hàng là bắt buộc khi không chọn khách hàng có sẵn',
            'customer_email.email' => 'Email không đúng định dạng',
            'customer_email.max' => 'Email không được vượt quá 255 ký tự',
            'customer_address.max' => 'Địa chỉ không được vượt quá 500 ký tự',
            'customer_id.exists' => 'Khách hàng không tồn tại trong hệ thống',
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
        // Store the uploaded document files in session if there's a validation error
        if ($this->hasFile('documents.0.document_file')) {
            $documentFile = $this->file('documents.0.document_file');
            $documentTemp = $documentFile->get();
            $documentBase64 = 'data:' . $documentFile->getMimeType() . ';base64,' . base64_encode($documentTemp);
            session()->flash('_documentFile0_temp', $documentBase64);
        } elseif ($this->has('_documentFile0_temp')) {
            // Preserve the previously uploaded file if it exists in the current request
            session()->flash('_documentFile0_temp', $this->input('_documentFile0_temp'));
        }

        if ($this->hasFile('documents.1.document_file')) {
            $documentFile = $this->file('documents.1.document_file');
            $documentTemp = $documentFile->get();
            $documentBase64 = 'data:' . $documentFile->getMimeType() . ';base64,' . base64_encode($documentTemp);
            session()->flash('_documentFile1_temp', $documentBase64);
        } elseif ($this->has('_documentFile1_temp')) {
            // Preserve the previously uploaded file if it exists in the current request
            session()->flash('_documentFile1_temp', $this->input('_documentFile1_temp'));
        }

        throw new HttpResponseException(
            redirect()
                ->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
