<?php

namespace App\Http\Requests\Vehicle;

use App\Http\Requests\Traits\UsesSystemDateFormat;
use Illuminate\Foundation\Http\FormRequest;

class StoreVehicleRequest extends FormRequest
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
        return [
            'plate_number' => 'required|string|unique:vehicles,plate_number',
            'vehicle_type_id' => 'required|exists:vehicle_types,vehicle_type_id',
            'driver_id' => 'nullable|exists:users,id',
            'capacity' => 'nullable|numeric|min:0',
            'manufactured_year' => 'nullable|integer|min:1990|max:' . date('Y'),
            'status' => 'required|in:active,maintenance,inactive',
            'documents' => 'array',
            'documents.*.document_type' => 'nullable|string|in:' . implode(',', array_keys(\App\Models\VehicleDocument::getDocumentTypes())),
            'documents.*.issue_date' => 'nullable|' . $this->getSystemDateFormatRule(),
            'documents.*.expiry_date' => 'nullable|' . $this->getSystemDateFormatRule() . '|after_or_equal:documents.*.issue_date',
            'documents.*.document_number' => 'nullable|string',
            'documents.*.document_file' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ];
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
        ];
    }
}
