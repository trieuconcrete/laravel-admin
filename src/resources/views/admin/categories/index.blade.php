@extends('admin.layout')
@section('title', 'Quản lý danh mục')

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
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="ri-add-circle-line align-middle me-1"></i> Thêm danh mục
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter -->
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <form method="GET" action="{{ route('admin.categories.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input name="search" value="{{ request('search') }}" type="text"
                                        class="form-control" placeholder="Tên hoặc mô tả...">
                                </div>
                                <div class="col-md-3 me-auto">
                                    <select class="form-select" name="is_active">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Hoạt
                                            động</option>
                                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Vô hiệu
                                            hóa</option>
                                    </select>
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
                                        <th>Tên danh mục</th>
                                        <th>Màu</th>
                                        <th>Thứ tự</th>
                                        <th>Trạng thái</th>
                                        <th>Số bài viết</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $cat)
                                        <tr>
                                            <td>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-outline-primary btn-show-category"
                                                        data-bs-toggle="modal" data-bs-target="#detailModal"
                                                        data-id="{{ $cat->id }}">
                                                        Chi tiết
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-category-btn"
                                                        data-id="{{ $cat->id }}">
                                                        Xóa
                                                    </button>
                                                    <form action="{{ route('admin.categories.destroy', $cat) }}"
                                                        method="POST" class="delete-category-form"
                                                        id="delete-form-{{ $cat->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                            <td>{{ $cat->name }}</td>
                                            <td>
                                                <span class="badge"
                                                    style="background-color: {{ $cat->color }}">{{ $cat->color }}</span>
                                            </td>
                                            <td>{{ $cat->order }}</td>
                                            <td>
                                                <span class="badge bg-{{ $cat->is_active ? 'success' : 'secondary' }}">
                                                    {{ $cat->is_active ? 'Hoạt động' : 'Vô hiệu' }}
                                                </span>
                                            </td>
                                            <td>{{ $cat->posts_count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{ $categories->links('vendor.pagination.bootstrap-5') }}

            </div>
        </div>
    </div>

    @include('admin.categories.partials.add_modal')
    @include('admin.categories.partials.edit_modal')
    @include('admin.categories.partials.detail_modal')

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Hiển thị chi tiết category
            $(document).on("click", ".btn-show-category", function() {
                let id = $(this).data("id");
                $.get(`/admin/categories/${id}`, function(res) {
                    // Fill modal chi tiết
                    $("#detail-name").text(res.name);
                    $("#detail-slug").text(res.slug);
                    $("#detail-color").text(res.color);
                    $("#detail-order").text(res.order);
                    $("#detail-description").text(res.description ?? "");
                    $("#detail-status").text(res.is_active ? "Hoạt động" : "Vô hiệu");

                    // Gắn id để dùng khi chuyển sang edit
                    $("#open-edit-from-detail").data("id", id);

                    // Mở modal
                    $("#detailCategoryModal").modal("show");
                });
            });

            // Từ chi tiết → chỉnh sửa
            $(document).on("click", "#open-edit-from-detail", function() {
                let id = $(this).data("id");

                if (id) {
                    $.get(`/admin/categories/${id}`, function(res) {
                        // Fill modal edit
                        $("#edit-category-form").attr("action", `/admin/categories/${res.id}`);
                        $("#edit-id").val(res.id);
                        $("#edit-name").val(res.name);
                        $("#edit-slug").val(res.slug);
                        $("#edit-color").val(res.color);
                        $("#edit-order").val(res.order);
                        $("#edit-description").val(res.description ?? "");
                        $("#edit-is_active").prop("checked", res.is_active == 1);

                        // Đóng detail, mở edit
                        $("#detailCategoryModal").modal("hide");
                        $("#editCategoryModal").modal("show");
                    });
                }
            });

            // Generate slug trong edit
            $("#generate-edit-slug").on("click", function() {
                const name = $("#edit-name").val();
                let slug = slugify(name);
                $("#edit-slug").val(slug);
            });

            // Hủy trong edit → quay lại detail
            $("#cancel-edit").on("click", function() {
                $("#editCategoryModal").modal("hide");
                $("#detailCategoryModal").modal("show");
            });

            // Submit thêm mới (AJAX POST)
            $("#add-category-form").on("submit", function(e) {
                e.preventDefault();
                let form = $(this);

                $.post(form.attr("action"), form.serialize())
                    .done(function() {
                        $("#addCategoryModal").modal("hide");
                        location.reload(); // hoặc append row mới
                    })
                    .fail(function(xhr) {
                        $(".error").text("");
                        $.each(xhr.responseJSON.errors, function(field, messages) {
                            $(`[data-field='${field}']`).text(messages[0]);
                        });
                    });
            });

            // Submit cập nhật (AJAX PUT)
            $("#edit-category-form").on("submit", function(e) {
                e.preventDefault();
                let form = $(this);

                $.ajax({
                        url: form.attr("action"),
                        method: "POST", // Laravel nhận PUT qua _method
                        data: form.serialize(),
                    })
                    .done(function() {
                        $("#editCategoryModal").modal("hide");
                        location.reload(); // hoặc cập nhật row trong bảng
                    })
                    .fail(function(xhr) {
                        $(".error").text("");
                        $.each(xhr.responseJSON.errors, function(field, messages) {
                            $(`[data-field='${field}']`).text(messages[0]);
                        });
                    });
            });

            // Xoá danh mục
            $(document).on("click", ".delete-category-btn", function(e) {
                e.preventDefault();

                let id = $(this).data("id");
                let form = $("#delete-form-" + id);

                Swal.fire({
                    title: "Bạn có chắc chắn muốn xoá?",
                    text: "Danh mục này sẽ bị xoá vĩnh viễn và không thể khôi phục!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: form.attr("action"),
                            type: "POST",
                            data: form.serialize(),
                            success: function() {
                                form.closest("tr").remove();

                                showToast('success',
                                    "Danh mục đã được xoá thành công.");
                                setTimeout(function() {
                                    location.reload();
                                }, 3000);
                            },
                            error: function() {
                                showToast('error', 'Có lỗi xảy ra khi xử lý');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
