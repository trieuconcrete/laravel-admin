<div class="col-md-6">
    <div class="mb-3">
        <label for="site_title" class="form-label">Tiêu đề trang web <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="site_title" name="system[site_title]" 
            value="{{ old('system.site_title', $settings['system']->where('key', 'site_title')->first()->value ?? '') }}">
    </div>
    
    <div class="mb-3">
        <label for="pagination_limit" class="form-label">Số lượng bản ghi mỗi trang <span class="text-danger">*</span></label>
        <input type="number" class="form-control" id="pagination_limit" name="system[pagination_limit]" min="5" max="100" 
            value="{{ old('system.pagination_limit', $settings['system']->where('key', 'pagination_limit')->first()->value ?? 15) }}">
    </div>
    
</div>

<div class="col-md-6">
    <div class="mb-3">
        <label for="date_format" class="form-label">Định dạng ngày tháng <span class="text-danger">*</span></label>
        <select class="form-select" id="date_format" name="system[date_format]">
            @php
                $currentDateFormat = old('system.date_format', $settings['system']->where('key', 'date_format')->first()->value ?? 'd/m/Y');
            @endphp
            <option value="Y-m-d" {{ $currentDateFormat == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2025-12-31)</option>
            <option value="d-m-Y" {{ $currentDateFormat == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (31-12-2025)</option>
        </select>
    </div>


    <div class="mb-3 form-check form-switch">
        <input class="form-check-input" type="checkbox" id="maintenance_mode" name="settings[maintenance_mode]" value="1"
            {{ old('settings.maintenance_mode', $settings['system']->where('key', 'maintenance_mode')->first()->value ?? false) ? 'checked' : '' }}>
        <label class="form-check-label" for="maintenance_mode">Chế độ bảo trì</label>
        <div class="text-muted small">Khi bật chế độ bảo trì, chỉ admin mới có thể truy cập hệ thống</div>
    </div>
</div>
