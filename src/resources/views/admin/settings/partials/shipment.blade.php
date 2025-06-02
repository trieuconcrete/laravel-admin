<div class="col-md-6">
    <div class="mb-3">
        <label for="default_distance_unit" class="form-label">Đơn vị khoảng cách mặc định <span class="text-danger">*</span></label>
        <select class="form-select" id="default_distance_unit" name="shipment[default_distance_unit]">
            @php
                $currentDistanceUnit = old('shipment.default_distance_unit', $settings['shipment']->where('key', 'default_distance_unit')->first()->value ?? 'km');
            @endphp
            <option value="km" {{ $currentDistanceUnit == 'km' ? 'selected' : '' }}>Kilômét (km)</option>
            <option value="mi" {{ $currentDistanceUnit == 'mi' ? 'selected' : '' }}>Dặm (mi)</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label for="default_weight_unit" class="form-label">Đơn vị trọng lượng mặc định <span class="text-danger">*</span></label>
        <select class="form-select" id="default_weight_unit" name="shipment[default_weight_unit]">
            @php
                $currentWeightUnit = old('shipment.default_weight_unit', $settings['shipment']->where('key', 'default_weight_unit')->first()->value ?? 'kg');
            @endphp
            <option value="kg" {{ $currentWeightUnit == 'kg' ? 'selected' : '' }}>Kilôgram (kg)</option>
            <option value="ton" {{ $currentWeightUnit == 'ton' ? 'selected' : '' }}>Tấn (ton)</option>
            <option value="lb" {{ $currentWeightUnit == 'lb' ? 'selected' : '' }}>Pound (lb)</option>
        </select>
    </div>
</div>

<div class="col-md-6">
    <div class="mb-3 form-check form-switch">
        <input type="hidden" name="shipment[auto_generate_shipment_code]" value="0">
        <input class="form-check-input" type="checkbox" id="auto_generate_shipment_code"
            name="shipment[auto_generate_shipment_code]" value="1"
            data-toggle-target="#shipment_code_prefix_container"
            {{ old('shipment.auto_generate_shipment_code', $settings['shipment']->where('key', 'auto_generate_shipment_code')->first()->value ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="auto_generate_shipment_code">Tự động tạo mã vận chuyển</label>
    </div>

    
    <div id="shipment_code_prefix_container" class="mb-3 {{ old('shipment.auto_generate_shipment_code', $settings['shipment']->where('key', 'auto_generate_shipment_code')->first()->value ?? true) ? '' : 'd-none' }}">
        <label for="shipment_code_prefix" class="form-label">Tiền tố mã vận chuyển <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="shipment_code_prefix" name="shipment[shipment_code_prefix]" 
            value="{{ old('shipment.shipment_code_prefix', $settings['shipment']->where('key', 'shipment_code_prefix')->first()->value ?? 'HPL') }}">
        <small class="text-muted">Mã vận chuyển sẽ có định dạng: [Tiền tố]-[Năm][Tháng][Ngày]-[Số thứ tự]</small>
        <div class="mt-2">
            <span class="badge bg-info">Ví dụ: HPL-20250531-001</span>
        </div>
    </div>
</div>
