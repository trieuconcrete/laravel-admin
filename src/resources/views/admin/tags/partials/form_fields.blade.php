<div class="mb-3">
    <label for="{{ $mode }}-name">Tên tag</label>
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
