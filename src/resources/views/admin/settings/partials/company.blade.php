<div class="col-md-6">
    <div class="mb-3">
        <label for="company_name" class="form-label">Tên công ty</label>
        <input type="text" class="form-control" id="company_name" name="company[company_name]" 
            value="{{ old('company.company_name', $settings['company']->where('key', 'company_name')->first()->value ?? '') }}">
    </div>
    
    <div class="mb-3">
        <label for="company_address" class="form-label">Địa chỉ</label>
        <textarea class="form-control" id="company_address" name="company[company_address]" rows="3">{{ old('company.company_address', $settings['company']->where('key', 'company_address')->first()->value ?? '') }}</textarea>
    </div>
    
    <div class="mb-3">
        <label for="company_phone" class="form-label">Số điện thoại</label>
        <input type="text" class="form-control" id="company_phone" name="company[company_phone]" 
            value="{{ old('company.company_phone', $settings['company']->where('key', 'company_phone')->first()->value ?? '') }}">
    </div>
    
    <div class="mb-3">
        <label for="company_email" class="form-label">Email</label>
        <input type="email" class="form-control" id="company_email" name="company[company_email]" 
            value="{{ old('company.company_email', $settings['company']->where('key', 'company_email')->first()->value ?? '') }}">
    </div>
</div>

<div class="col-md-6">
    <div class="mb-3">
        <label for="company_tax_code" class="form-label">Mã số thuế</label>
        <input type="text" class="form-control" id="company_tax_code" name="company[company_tax_code]" 
            value="{{ old('company.company_tax_code', $settings['company']->where('key', 'company_tax_code')->first()->value ?? '') }}">
    </div>
    @php
        $logoPath = $settings['company']->where('key', 'company_logo')->first()->value ?? '';
    @endphp
    <div class="mb-4">
        <label for="company_logo" class="form-label">Logo công ty</label>
        <input type="file" name="company[company_logo]" id="company_logo" class="form-control mt-1 border p-2 rounded">
        <small class="text-muted">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB</small>
        @if($logoPath)
            <div class="mt-2">
                <img id="company_logo_preview" src="{{ $logoPath ? asset('storage/' . $logoPath) : asset('no-image.jpeg') }}" class="w-24 h-24 rounded-full mt-4" alt="Logo Preview">
            </div>
        @endif
    </div>
</div>

<script>
document.getElementById('company_logo').addEventListener('change', function(event) {
        const file = event.target.files[0];
    
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('company_logo_preview').src = e.target.result;
            }
            
            reader.readAsDataURL(file);
        }
    });
</script>
