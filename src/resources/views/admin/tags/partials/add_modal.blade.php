<div class="modal fade" id="addTagModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="add-tag-form" action="{{ route('admin.tags.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm tag mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.tags.partials.form_fields', ['mode' => 'create'])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
