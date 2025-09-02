<?php

namespace App\Http\Requests\Vehicle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleTypeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $vehicleTypeId = $this->route('id') ?? $this->vehicle_type_id;
        
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('vehicle_types', 'name')->ignore($vehicleTypeId, 'vehicle_type_id')
            ],
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|boolean'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Tên loại xe là bắt buộc',
            'name.unique' => 'Tên loại xe đã tồn tại',
            'name.max' => 'Tên loại xe không được vượt quá 255 ký tự',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'tên loại xe',
            'description' => 'mô tả',
            'status' => 'trạng thái'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Chuyển đổi status từ checkbox
        $this->merge([
            'status' => $this->has('status') ? 1 : 0,
        ]);
    }
}