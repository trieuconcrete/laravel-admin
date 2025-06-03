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
            'company.company_name' => 'required|string|max:255',
            'company.company_address' => 'required|string',
            'company.company_phone' => 'required|string|max:20',
            'company.company_email' => 'required|email|max:255',
            'company.company_tax_code' => 'required|string|max:50',
            'company.company_bank_account' => 'required|string|max:50',
            'company.company_bank_name' => 'required|string|max:100',
            'company.company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
            // System settings
            'system' => 'nullable|array',
            'system.site_title' => 'required|string|max:255',
            'system.pagination_limit' => 'required|integer|min:5|max:100',
            'system.date_format' => 'required|string',
            'system.time_format' => 'required|string',
            'system.timezone' => 'required|string',
            
            // Shipment settings
            'shipment' => 'nullable|array',
            'shipment.default_distance_unit' => 'required|string',
            'shipment.default_weight_unit' => 'required|string',
            'shipment.shipment_code_prefix' => 'required_if:shipment.auto_generate_shipment_code,1|string|max:10',
            
            // Notification settings
            'notifications' => 'nullable|array',
            'notifications.notification_email' => 'required_if:notifications.email_notifications,1|email',
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
            'company.company_name.required' => 'Tên công ty là bắt buộc',
            'company.company_name.max' => 'Tên công ty không được vượt quá 255 ký tự',
            'company.company_address.required' => 'Địa chỉ công ty là bắt buộc',
            'company.company_phone.required' => 'Số điện thoại công ty là bắt buộc',
            'company.company_phone.max' => 'Số điện thoại không được vượt quá 20 ký tự',
            'company.company_email.required' => 'Email công ty là bắt buộc',
            'company.company_email.email' => 'Email công ty không hợp lệ',
            'company.company_email.max' => 'Email không được vượt quá 255 ký tự',
            'company.company_tax_code.required' => 'Mã số thuế là bắt buộc',
            'company.company_tax_code.max' => 'Mã số thuế không được vượt quá 50 ký tự',
            'company.company_bank_account.required' => 'Tài khoản ngân hàng là bắt buộc',
            'company.company_bank_account.max' => 'Tài khoản ngân hàng không được vượt quá 50 ký tự',
            'company.company_bank_name.required' => 'Tên ngân hàng là bắt buộc',
            'company.company_bank_name.max' => 'Tên ngân hàng không được vượt quá 100 ký tự',
            'company.company_logo.image' => 'Logo công ty phải là một hình ảnh',
            'company.company_logo.mimes' => 'Logo công ty phải có định dạng: jpeg, png, jpg, gif',
            'company.company_logo.max' => 'Logo công ty không được vượt quá 2MB',
            
            // System settings
            'system.array' => 'Cài đặt hệ thống phải là một mảng',
            'system.site_title.required' => 'Tiêu đề trang web là bắt buộc',
            'system.site_title.max' => 'Tiêu đề trang web không được vượt quá 255 ký tự',
            'system.pagination_limit.required' => 'Số lượng bản ghi mỗi trang là bắt buộc',
            'system.pagination_limit.integer' => 'Số lượng bản ghi mỗi trang phải là số nguyên',
            'system.pagination_limit.min' => 'Số lượng bản ghi mỗi trang tối thiểu là 5',
            'system.pagination_limit.max' => 'Số lượng bản ghi mỗi trang tối đa là 100',
            'system.date_format.required' => 'Định dạng ngày tháng là bắt buộc',
            'system.time_format.required' => 'Định dạng thời gian là bắt buộc',
            'system.timezone.required' => 'Múi giờ là bắt buộc',
            
            // Shipment settings
            'shipment.array' => 'Cài đặt vận chuyển phải là một mảng',
            'shipment.default_distance_unit.required' => 'Đơn vị khoảng cách mặc định là bắt buộc',
            'shipment.default_weight_unit.required' => 'Đơn vị trọng lượng mặc định là bắt buộc',
            'shipment.shipment_code_prefix.required_if' => 'Tiền tố mã vận chuyển là bắt buộc khi bật tự động tạo mã vận chuyển',
            'shipment.shipment_code_prefix.max' => 'Tiền tố mã vận chuyển không được vượt quá 10 ký tự',
            
            // Notification settings
            'notifications.array' => 'Cài đặt thông báo phải là một mảng',
            'notifications.notification_email.required_if' => 'Email thông báo là bắt buộc khi bật gửi thông báo qua email',
            'notifications.notification_email.email' => 'Email thông báo không hợp lệ',
        ];
    }
}
