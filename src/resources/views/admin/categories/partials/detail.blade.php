<div class="row">
    <div class="col-12">
        <div class="row mb-3">
            <div class="col-4 fw-bold">Tên danh mục:</div>
            <div class="col-8">{{ $category->name }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-4 fw-bold">Slug:</div>
            <div class="col-8">{{ $category->slug }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-4 fw-bold">Màu:</div>
            <div class="col-8">
                <span
                    style="display:inline-block;width:20px;height:20px;background:{{ $category->color }};border:1px solid #ccc;"></span>
                {{ $category->color }}
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-4 fw-bold">Thứ tự:</div>
            <div class="col-8">{{ $category->order }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-4 fw-bold">Mô tả:</div>
            <div class="col-8">{{ $category->description || "Không có" }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-4 fw-bold">Trạng thái:</div>
            <div class="col-8">
                @if ($category->is_active)
                    <span class="badge bg-success">Hoạt động</span>
                @else
                    <span class="badge bg-secondary">Không hoạt động</span>
                @endif
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-4 fw-bold">Ngày tạo:</div>
            <div class="col-8">{{ $category->created_at->format('d/m/Y H:i') }}</div>
        </div>

        <div class="row mb-3">
            <div class="col-4 fw-bold">Cập nhật lần cuối:</div>
            <div class="col-8">{{ $category->updated_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</div>
