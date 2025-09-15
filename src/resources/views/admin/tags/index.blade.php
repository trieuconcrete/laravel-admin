@extends('admin.layout')
@section('title', 'Quản lý tags')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">

                <!-- Header -->
                <div class="row mb-3 pb-1">
                    <div class="col-12 d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="me-3 btn btn-outline-primary ">
                            Quay lại
                        </a>
                        <div>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTagModal">
                                <i class="ri-add-circle-line align-middle me-1"></i> Thêm tag
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter -->
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <form method="GET" action="{{ route('admin.tags.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4 me-auto">
                                    <input name="search" value="{{ request('search') }}" type="text"
                                        class="form-control" placeholder="Tên hoặc mô tả...">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-primary w-100">Tìm kiếm</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- List -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table table-hover align-middle table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Thao tác</th>
                                        <th>Tên tag</th>
                                        <th>Slug</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tags as $tag)
                                        <tr>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary btn-show-tag"
                                                        data-bs-toggle="modal" data-bs-target="#detailTagModal"
                                                        data-id="{{ $tag->id }}">
                                                        Chi tiết
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-tag-btn"
                                                        data-id="{{ $tag->id }}">
                                                        Xóa
                                                    </button>
                                                    <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST"
                                                        class="delete-tag-form" id="delete-form-{{ $tag->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                            <td>{{ $tag->name }}</td>
                                            <td>{{ $tag->slug }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{ $tags->links('vendor.pagination.bootstrap-5') }}

            </div>
        </div>
    </div>

    <!-- Include Modals -->
    @include('admin.tags.partials.add_modal')
    @include('admin.tags.partials.detail_modal')
    @include('admin.tags.partials.edit_modal')
@endsection

@push('scripts')
    <script>
        $(function() {
            // Xóa tag
            $('.delete-tag-btn').click(function() {
                const id = $(this).data('id');
                const form = $('#delete-form-' + id);

                Swal.fire({
                    title: 'Xóa tag này?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                }).then((r) => {
                    if (r.isConfirmed) form.submit();
                });
            });

            // Thêm tag
            $('#add-tag-form').submit(function(e) {
                e.preventDefault();
                const $form = $(this);
                const url = $form.attr('action');
                const formData = new FormData(this);
                $form.find('.error').text('');

                $.ajax({
                    url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        bootstrap.Modal.getInstance($('#addTagModal')).hide();
                        $form[0].reset();
                        location.reload()
                        showToast('success', "Tag được tạo thành công.");
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, (f, msg) => {
                                $form.find(`.error[data-field="${f}"]`).text(msg[0]);
                            });
                        }
                    }
                });
            });

            // Hiển thị chi tiết
            $('.btn-show-tag').click(function() {
                const id = $(this).data('id');
                $('#detail-name').text('Đang tải...');
                $('#detail-slug').text('');
                $('#detailTagBtn').data('id', id);

                $.get(`/admin/tags/${id}`, function(data) {
                    $('#detail-name').text(data.name);
                    $('#detail-slug').text(data.slug);
                }).fail(() => {
                    $('#detail-name').text('Lỗi tải dữ liệu');
                });
            });


            // Mở form cập nhật
            $('#detailTagBtn').on('click', function() {
                let id = $(this).data('id');
                if (id) {
                    $.get(`/admin/tags/${id}/edit`, function(data) {
                        $('#edit-id').val(data.id);
                        $('#edit-name').val(data.name);
                        $('#edit-slug').val(data.slug);

                        $('#edit-tag-form').attr('action', `/admin/tags/${id}`);
                        $('#detailTagModal').modal('hide');
                        $('#editTagModal').modal('show');
                    });
                }
            });

            // Tạo lại slug
            $('#generate-edit-slug').click(function() {
                let name = $('#edit-name').val();
                let slug = slugify(name);
                $('#edit-slug').val(slug);
            });

            // Cập nhật tag
            $('#edit-tag-form').submit(function(e) {
                e.preventDefault();
                const $form = $(this);
                const url = $form.attr('action');
                const formData = new FormData(this);
                $form.find('.error').text('');

                formData.append('_method', 'PUT');

                $.ajax({
                    url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        bootstrap.Modal.getInstance($('#editTagModal')).hide();
                        location.reload()
                        showToast('success', "Tag đã cập nhật thành công.");
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            $.each(xhr.responseJSON.errors, (f, msg) => {
                                $form.find(`.error[data-field="${f}"]`).text(msg[0]);
                            });
                        }
                    }
                });
            });
        });
    </script>
@endpush
