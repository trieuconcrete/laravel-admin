<form id="editQuoteForm">
    @csrf
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Khách hàng <span class="text-danger">*</span></label>
            <select class="form-select js-example-basic-single" name="customer_id">
                <option value="">Chọn khách hàng</option>
                @foreach ($customers as $key => $customer)
                    <option value="{{ $key }}"
                        {{ old('customer_id', $quote->customer_id) === $key ? 'selected' : '' }}>
                        {{ $customer }}
                    </option>
                @endforeach
            </select>
            <div class="text-danger error" data-field="customer_id"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Trạng thái </label>
            <select class="form-select" name="status">
                <option value="">Chọn loại báo giá </option>
                @foreach ($quoteStatuses as $val => $label)
                    <option value="{{ $val }}"
                        {{ old('status', $quote->status) === $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <div class="text-danger error" data-field="status"></div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
            <input 
                type="date" class="form-control" name="pickup_datetime" 
                value="@formatDateForInput($quote?->pickup_datetime)"
            >
            <div class="text-danger error" data-field="pickup_datetime"></div>
        </div>
        <div class="col-md-6">
            <label class="form-label">Ngày hết hạn</label>
            <input 
                type="date" class="form-control" name="valid_until"
                value="@formatDateForInput($quote?->valid_until)"
            >
            <div class="text-danger error" data-field="valid_until"></div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Mô tả dịch vụ</label>
        <textarea class="form-control" rows="3" placeholder="Nhập Mô tả dịch vụ" name="cargo_description">{!! old('cargo_description', $quote->cargo_description ) !!}</textarea>
        <div class="text-danger error" data-field="cargo_description"></div>
    </div>

    <div class="mb-3">
        <label class="form-label">Ghi chú</label>
        <textarea class="form-control" rows="3" placeholder="Nhập ghi chú" name="notes">{!! old('notes', $quote->notes ) !!}</textarea>
        <div class="text-danger error" data-field="notes"></div>
    </div>
    <div class="mb-3">
        <label class="form-label">File báo giá <span class="text-danger">*</span></label>
        <input type="file" class="form-control" name="document_file" >
        @php
            $quoteAttachment = $quote->attachments ? $quote->attachments->first() : null;
        @endphp
        @if ($quoteAttachment && $quoteAttachment->file_path)
            <input type="file" class="form-control" name="document_id" value="{{ $quoteAttachment->id }}" hidden>
            <div class="mt-2">
                <a href="{{ $quoteAttachment->document_file_url }}" target="_blank">
                    📎 Xem tệp đã tải lên
                </a>
            </div>
        @endif
        <div class="text-danger error" data-field="document_file"></div>
    </div>
</form>
