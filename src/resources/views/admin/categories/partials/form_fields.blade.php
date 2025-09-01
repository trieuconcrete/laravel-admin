<div class="mb-3">
    <label for="{{ $mode }}-name">Tên danh mục</label>
    <input type="text" class="form-control" name="name" id="{{ $mode }}-name">
    <div class="text-danger error" data-field="name"></div>
</div>

<div class="mb-3">
    <label for="{{ $mode }}-slug">Slug</label>
    <div class="input-group">
        <input type="text" class="form-control" name="slug" id="{{ $mode }}-slug"
            placeholder="Tự động sinh nếu để trống">
        @if ($mode === 'edit')
            <button type="button" class="btn btn-outline-primary" id="generate-{{ $mode }}-slug">
                Tạo lại slug
            </button>
        @endif
    </div>
    <div class="text-danger error" data-field="slug"></div>
</div>

<div class="mb-3">
    <label for="{{ $mode }}-color">Màu</label>
    <input type="color" class="form-control form-control-color" name="color" id="{{ $mode }}-color"
        value="#3B82F6">
    <div class="text-danger error" data-field="color"></div>
</div>

<div class="mb-3">
    <label for="{{ $mode }}-order">Thứ tự</label>
    <input type="number" class="form-control" name="order" id="{{ $mode }}-order" value="0">
    <div class="text-danger error" data-field="order"></div>
</div>

<div class="mb-3">
    <label for="{{ $mode }}-description">Mô tả</label>
    <textarea class="form-control" name="description" id="{{ $mode }}-description"></textarea>
    <div class="text-danger error" data-field="description"></div>
</div>

<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_active" id="{{ $mode }}-is_active" value="1"
        {{ $mode === 'create' ? 'checked' : '' }}>
    <label class="form-check-label" for="{{ $mode }}-is_active">Hoạt động</label>
</div>
