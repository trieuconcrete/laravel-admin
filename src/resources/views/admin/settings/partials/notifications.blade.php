<div class="col-md-6">
    <div class="mb-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" id="email_notifications" name="notifications[email_notifications]" value="1" data-toggle-target="#email_notification_settings"
            {{ old('notifications.email_notifications', $settings['notifications']->where('key', 'email_notifications')->first()->value ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="email_notifications">Gửi thông báo qua email</label>
    </div>
    
    <div id="email_notification_settings" class="mb-3 {{ old('notifications.email_notifications', $settings['notifications']->where('key', 'email_notifications')->first()->value ?? true) ? '' : 'd-none' }}">
        <label for="notification_email" class="form-label">Email gửi thông báo <span class="text-danger">*</span></label>
        <input type="email" class="form-control" id="notification_email" name="notifications[notification_email]" 
            value="{{ old('notifications.notification_email', $settings['notifications']->where('key', 'notification_email')->first()->value ?? '') }}">
        <small class="text-muted">Email này sẽ được sử dụng để gửi thông báo tự động</small>
    </div>
</div>

<div class="col-md-6">
    <div class="mb-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" id="sms_notifications" name="notifications[sms_notifications]" value="1" data-toggle-target="#sms_notification_settings"
            {{ old('notifications.sms_notifications', $settings['notifications']->where('key', 'sms_notifications')->first()->value ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="sms_notifications">Gửi thông báo qua SMS</label>
    </div>
    
    <div id="sms_notification_settings" class="mb-3 {{ old('notifications.sms_notifications', $settings['notifications']->where('key', 'sms_notifications')->first()->value ?? false) ? '' : 'd-none' }}">
        <div class="alert alert-info">
            <i class="ri-information-line me-2"></i> Để sử dụng tính năng gửi SMS, bạn cần cấu hình API SMS trong phần cài đặt nâng cao.
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Các sự kiện gửi thông báo <span class="text-danger">*</span></label>

        <div class="form-check">
            <input type="hidden" name="notifications[notify_new_shipment]" value="0">
            <input class="form-check-input" type="checkbox" id="notify_new_shipment"
                name="notifications[notify_new_shipment]" value="1"
                {{ old('notifications.notify_new_shipment', $settings['notifications']->where('key', 'notify_new_shipment')->first()->value ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="notify_new_shipment">Khi tạo vận chuyển mới</label>
        </div>

        <div class="form-check">
            <input type="hidden" name="notifications[notify_shipment_status]" value="0">
            <input class="form-check-input" type="checkbox" id="notify_shipment_status"
                name="notifications[notify_shipment_status]" value="1"
                {{ old('notifications.notify_shipment_status', $settings['notifications']->where('key', 'notify_shipment_status')->first()->value ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="notify_shipment_status">Khi cập nhật trạng thái vận chuyển</label>
        </div>

        <div class="form-check">
            <input type="hidden" name="notifications[notify_vehicle_maintenance]" value="0">
            <input class="form-check-input" type="checkbox" id="notify_vehicle_maintenance"
                name="notifications[notify_vehicle_maintenance]" value="1"
                {{ old('notifications.notify_vehicle_maintenance', $settings['notifications']->where('key', 'notify_vehicle_maintenance')->first()->value ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="notify_vehicle_maintenance">Khi xe cần bảo dưỡng</label>
        </div>
    </div>

</div>
