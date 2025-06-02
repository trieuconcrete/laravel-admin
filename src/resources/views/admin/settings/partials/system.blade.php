<div class="col-md-6">
    <div class="mb-3">
        <label for="site_title" class="form-label">Tiêu đề trang web</label>
        <input type="text" class="form-control" id="site_title" name="system[site_title]" 
            value="{{ old('system.site_title', $settings['system']->where('key', 'site_title')->first()->value ?? '') }}">
    </div>
    
    <div class="mb-3">
        <label for="pagination_limit" class="form-label">Số lượng bản ghi mỗi trang</label>
        <input type="number" class="form-control" id="pagination_limit" name="system[pagination_limit]" min="5" max="100" 
            value="{{ old('system.pagination_limit', $settings['system']->where('key', 'pagination_limit')->first()->value ?? 15) }}">
    </div>
    
    <div class="mb-3">
        <label for="date_format" class="form-label">Định dạng ngày tháng</label>
        <select class="form-select" id="date_format" name="system[date_format]">
            @php
                $currentDateFormat = old('system.date_format', $settings['system']->where('key', 'date_format')->first()->value ?? 'd/m/Y');
            @endphp
            <option value="d/m/Y" {{ $currentDateFormat == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (31/12/2025)</option>
            <option value="m/d/Y" {{ $currentDateFormat == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (12/31/2025)</option>
            <option value="Y-m-d" {{ $currentDateFormat == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2025-12-31)</option>
            <option value="d-m-Y" {{ $currentDateFormat == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (31-12-2025)</option>
        </select>
    </div>
</div>

<div class="col-md-6">
    <div class="mb-3">
        <label for="time_format" class="form-label">Định dạng thời gian</label>
        <select class="form-select" id="time_format" name="system[time_format]">
            @php
                $currentTimeFormat = old('system.time_format', $settings['system']->where('key', 'time_format')->first()->value ?? 'H:i');
            @endphp
            <option value="H:i" {{ $currentTimeFormat == 'H:i' ? 'selected' : '' }}>24 giờ (14:30)</option>
            <option value="h:i A" {{ $currentTimeFormat == 'h:i A' ? 'selected' : '' }}>12 giờ (02:30 PM)</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="timezone" class="form-label">Múi giờ</label>
        <select class="form-select" id="timezone" name="system[timezone]">
            @php
                $currentTimezone = old('system.timezone', $settings['system']->where('key', 'timezone')->first()->value ?? 'Asia/Ho_Chi_Minh');
            @endphp
            <option value="Asia/Ho_Chi_Minh" {{ $currentTimezone == 'Asia/Ho_Chi_Minh' ? 'selected' : '' }}>Hồ Chí Minh (GMT+7)</option>
            <option value="Asia/Bangkok" {{ $currentTimezone == 'Asia/Bangkok' ? 'selected' : '' }}>Bangkok (GMT+7)</option>
            <option value="Asia/Singapore" {{ $currentTimezone == 'Asia/Singapore' ? 'selected' : '' }}>Singapore (GMT+8)</option>
            <option value="Asia/Tokyo" {{ $currentTimezone == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo (GMT+9)</option>
            <option value="Europe/London" {{ $currentTimezone == 'Europe/London' ? 'selected' : '' }}>London (GMT+0)</option>
            <option value="America/New_York" {{ $currentTimezone == 'America/New_York' ? 'selected' : '' }}>New York (GMT-5)</option>
        </select>
    </div>
    
    <div class="mb-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="settings[maintenance_mode]" value="1"
            {{ old('settings.maintenance_mode', $settings['system']->where('key', 'maintenance_mode')->first()->value ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="maintenance_mode">Chế độ bảo trì</label>
        <div class="text-muted small">Khi bật chế độ bảo trì, chỉ admin mới có thể truy cập hệ thống</div>
    </div>
</div>
