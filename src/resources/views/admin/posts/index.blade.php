@extends('admin.layout')
@section('title', 'Quản lý bài viết')

@push('styles')
    <style>
        /* Custom styles */
        .table-wrapper {
            padding: 16px;
            box-sizing: border-box;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            /* table-layout: fixed; */
            min-width: 1000px;

        }

        .table td,
        .table th {
            white-space: nowrap;
        }

        .post-status {
            font-size: 12px;
            font-weight: 500;
            border-radius: 4px;
        }

        .status-published {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-draft {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-archived {
            background-color: #f3f4f6;
            color: #374151;
        }

        /* Stats card hover effect */
        .stat-card {
            transition: all 0.3s ease;
        }

        .form-check-input {
            cursor: pointer;
        }

        .post-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            flex-shrink: 0;
            image-rendering: optimizeQuality;
        }

        .post-category {
            font-size: 12px;
            font-weight: 500;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">

                <!-- Header: Stats Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1 text-muted small">Tổng bài viết</p>
                                    <h5 class="fw-bold mb-0">{{ $stats['total'] }}</h5>
                                </div>
                                <div class="bg-primary bg-opacity-10 rounded px-3 py-2">
                                    <i class="ri-news-fill text-primary fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1 text-muted small">Đã xuất bản</p>
                                    <h5 class="fw-bold text-success mb-0">{{ $stats['published'] }}</h5>
                                </div>
                                <div class="bg-success bg-opacity-10 rounded px-3 py-2">
                                    <i class="ri-file-upload-line text-success fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1 text-muted small">Bản nháp</p>
                                    <h5 class="fw-bold text-warning mb-0">{{ $stats['draft'] }}</h5>
                                </div>
                                <div class="bg-warning bg-opacity-10 rounded px-3 py-2">
                                    <i class="ri-draft-fill text-warning fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-3">
                        <div class="card shadow-sm h-100">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <p class="mb-1 text-muted small">Lượt xem</p>
                                    <h5 class="fw-bold text-purple mb-0">{{ number_format($stats['views']) }}</h5>
                                </div>
                                <div class="bg-secondary bg-opacity-10 rounded px-3 py-2">
                                    <i class="ri-eye-line text-secondary fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="row mb-3 pb-1">
                    <div class="col-12 d-flex justify-content-end gap-3">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-primary">Danh mục</a>
                        <a href="{{ route('admin.tags.index') }}" class="btn btn-outline-primary">Tags</a>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <form method="GET" action="{{ route('admin.posts.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4 me-auto">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Tìm kiếm bài viết...">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">Tất cả trạng thái</option>
                                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                            Đã xuất bản</option>
                                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bản nháp
                                        </option>
                                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>
                                            Lưu trữ</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="category_id" class="form-select">
                                        <option value="">Tất cả danh mục</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="date_filter" class="form-select">
                                        <option value="">Tất cả thời gian</option>
                                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>
                                            Hôm nay</option>
                                        <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>7
                                            ngày qua</option>
                                        <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>30
                                            ngày qua</option>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex justify-content-end">
                                    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i> Thêm mới
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Posts Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table table-hover align-middle table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><input type="checkbox" id="select-all" class="form-check-input"></th>
                                        <th>Bài viết</th>
                                        <th>Danh mục</th>
                                        <th>Tác giả</th>
                                        <th>Trạng thái</th>
                                        <th>Lượt xem</th>
                                        <th>Ngày tạo</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($posts as $post)
                                        <tr class="post-row" data-id="{{ $post->id }}">
                                            <td><input type="checkbox" class="form-check-input post-checkbox"
                                                    value="{{ $post->id }}"></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($post->featured_image)
                                                        <img src="{{ $post->featured_image_url }}"
                                                            alt="{{ $post->title }}" class="rounded me-3 post-image">
                                                    @else
                                                        <div
                                                            class="bg-light d-flex align-items-center justify-content-center me-3 post-image rounded">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold post-title">
                                                            {{ Str::limit($post->title, 50) }}</div>
                                                        <small
                                                            class="text-muted">{{ Str::limit($post->excerpt, 60) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if ($post->category)
                                                    <span class="px-2 py-1 rounded"
                                                        style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }}">
                                                        {{ $post->category->name }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Không có</span>
                                                @endif
                                            </td>
                                            <td>{{ $post->author->full_name ?? 'Không rõ' }}</td>
                                            <td><span
                                                    class="px-2 py-1 post-status {{ 'status-' . $post->status }}">{{ $post->getStatuses()[$post->status] }}</span>
                                            </td>
                                            <td>{{ number_format($post->views) }}</td>
                                            <td>{{ $post->created_at->format('d/m/Y') }}</td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.posts.edit', $post) }}"
                                                        class="btn btn-sm btn-outline-primary">Chỉnh sửa</a>
                                                    <a href="{{ route('admin.posts.preview', $post) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-success">Xem trước</a>
                                                    <button class="btn btn-sm btn-outline-danger delete-btn"
                                                        data-id="{{ $post->id }}">Xóa</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                <i class="fas fa-newspaper fs-2 mb-2 d-block"></i>Chưa có bài viết nào
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                @if ($posts->hasPages())
                    <div class="mt-3">
                        {{ $posts->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div id="bulk-actions"
        class="position-fixed bottom-0 start-50 translate-middle-x bg-white shadow p-3 rounded d-none z-3">
        <div class="d-flex align-items-center gap-3">
            <span class="small text-muted"><span id="selected-count">0</span> mục đã chọn</span>
            <button id="bulk-publish" class="btn btn-success btn-sm"><i class="fas fa-check me-1"></i>Xuất bản</button>
            <button id="bulk-draft" class="btn btn-secondary btn-sm"><i class="fas fa-edit me-1"></i>Chuyển nháp</button>
            <button id="bulk-delete" class="btn btn-danger btn-sm"><i class="fas fa-trash me-1"></i>Xóa</button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // Auto submit form on filter change
            $('#filter-form select').change(function() {
                $('#filter-form').submit();
            });

            // Search with debounce
            let searchTimeout;
            $('input[name="search"]').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    $('#filter-form').submit();
                }, 500);
            });

            // Select all checkbox
            $('#select-all').change(function() {
                $('.post-checkbox').prop('checked', $(this).prop('checked'));
                updateBulkActions();
            });

            // Individual checkbox
            $(document).on('change', '.post-checkbox', function() {
                updateBulkActions();
            });

            // Update bulk actions
            function updateBulkActions() {
                const checkedCount = $('.post-checkbox:checked').length;
                if (checkedCount > 0) {
                    $('#bulk-actions').removeClass('d-none');
                    $('#selected-count').text(checkedCount);
                } else {
                    $('#bulk-actions').addClass('d-none');
                }
            }

            // Delete post
            $(document).on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                const row = $(this).closest('tr');
                const title = row.find('.post-title').first().text();

                Swal.fire({
                    title: 'Xóa bài viết?',
                    html: `Bạn có chắc muốn xóa bài viết "<strong>${title}</strong>"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#ef4444',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        deletePost(id);
                    }
                });
            });

            function deletePost(id) {
                $.ajax({
                    url: `/admin/posts/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            showToast('success', response.message);
                            $(`.post-row[data-id="${id}"]`).fadeOut(400, function() {
                                $(this).remove();
                                updateBulkActions();
                            });
                        }
                    },
                    error: function() {
                        showToast('error', 'Có lỗi xảy ra khi xóa bài viết');
                    }
                });
            }

            // Bulk actions
            $('#bulk-publish').click(function() {
                const count = $('.post-checkbox:checked').length;

                Swal.fire({
                    title: 'Xuất bản bài viết?',
                    text: `Bạn sắp xuất bản ${count} bài viết.`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-check mr-2"></i>Xuất bản',
                    cancelButtonText: 'Hủy',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkUpdate('publish');
                    }
                });
            });

            $('#bulk-draft').click(function() {
                const count = $('.post-checkbox:checked').length;

                Swal.fire({
                    title: 'Chuyển về bản nháp?',
                    text: `Bạn sắp chuyển ${count} bài viết về bản nháp.`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-edit mr-2"></i>Chuyển nháp',
                    cancelButtonText: 'Hủy',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkUpdate('draft');
                    }
                });
            });

            $('#bulk-delete').click(function() {
                const count = $('.post-checkbox:checked').length;

                Swal.fire({
                    title: 'Xóa nhiều bài viết?',
                    html: `
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-5xl text-yellow-500 mb-4"></i>
                    <p>Bạn sắp xóa <strong>${count}</strong> bài viết.</p>
                    <p class="text-sm text-red-600 mt-2">Hành động này không thể hoàn tác!</p>
                </div>
            `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-trash mr-2"></i>Xóa tất cả',
                    cancelButtonText: 'Hủy',
                    confirmButtonColor: '#ef4444',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        bulkUpdate('delete');
                    }
                });
            });

            function bulkUpdate(action) {
                const ids = $('.post-checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (ids.length === 0) {
                    showToast('warning', 'Vui lòng chọn ít nhất một bài viết');

                    return;
                }

                $.post('/admin/posts/bulk-update', {
                    ids: ids,
                    action: action
                }, function(response) {
                    if (response.success) {
                        showToast('success', response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    }
                }).fail(function() {
                    showToast('error', 'Có lỗi xảy ra khi xử lý');
                });
            }

            // Refresh button
            $('#refresh-btn').click(function() {
                $(this).find('i').addClass('fa-spin');
                location.reload();
            });

            // Show success/error messages from session
            @if (session('success'))
                showToast('success', '{{ session('success') }}');
            @endif

            @if (session('error'))
                showToast('error', '{{ session('error') }}');
            @endif
        });
    </script>
@endpush
