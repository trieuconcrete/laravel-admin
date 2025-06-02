<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'group' => 'required|string|in:company,system,shipment,notifications',
            
            // Company settings
            'company' => 'nullable|array',
            'company.*' => 'nullable',
            'company.logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // System settings
            'system' => 'nullable|array',
            'system.*' => 'nullable',
            'system.pagination_limit' => 'nullable|integer|min:5|max:100',
            
            // Shipment settings
            'shipment' => 'nullable|array',
            'shipment.*' => 'nullable',
            
            // Notification settings
            'notifications' => 'nullable|array',
            'notifications.*' => 'nullable',
            'notifications.notification_email' => 'nullable|email',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'group.required' => 'Nhóm cài đặt là bắt buộc',
            'group.in' => 'Nhóm cài đặt không hợp lệ',
            
            // Company settings
            'company.array' => 'Cài đặt công ty phải là một mảng',
            'company.logo.image' => 'Logo công ty phải là một hình ảnh',
            'company.logo.mimes' => 'Logo công ty phải có định dạng: jpeg, png, jpg, gif',
            'company.logo.max' => 'Logo công ty không được vượt quá 2MB',
            
            // System settings
            'system.array' => 'Cài đặt hệ thống phải là một mảng',
            'system.pagination_limit.integer' => 'Số lượng bản ghi mỗi trang phải là số nguyên',
            'system.pagination_limit.min' => 'Số lượng bản ghi mỗi trang tối thiểu là 5',
            'system.pagination_limit.max' => 'Số lượng bản ghi mỗi trang tối đa là 100',
            
            // Shipment settings
            'shipment.array' => 'Cài đặt vận chuyển phải là một mảng',
            
            // Notification settings
            'notifications.array' => 'Cài đặt thông báo phải là một mảng',
            'notifications.notification_email.email' => 'Email thông báo không hợp lệ',
        ];
    }
}
