@extends('layouts.admin')

@section('title', 'Quản lý bài viết')

@push('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    /* Custom styles */
    .post-thumbnail {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-weight: 500;
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
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Tổng bài viết</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-newspaper text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Đã xuất bản</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['published'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Bản nháp</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['draft'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-edit text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-lg shadow-sm p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Lượt xem</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['views']) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-eye text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('admin.posts.index') }}" id="filter-form">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <div class="relative">
                        <input type="text" 
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Tìm kiếm bài viết..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">Tất cả trạng thái</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                    </select>
                </div>
                
                <!-- Category Filter -->
                <div>
                    <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Date Filter -->
                <div>
                    <select name="date_filter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">Tất cả thời gian</option>
                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Hôm nay</option>
                        <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>7 ngày qua</option>
                        <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>30 ngày qua</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Posts Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bài viết</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Danh mục</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tác giả</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lượt xem</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($posts as $post)
                    <tr class="post-row hover:bg-gray-50" data-id="{{ $post->id }}">
                        <td class="px-6 py-4">
                            <input type="checkbox" class="post-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $post->id }}">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($post->featured_image)
                                    <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}" class="post-thumbnail mr-3">
                                @else
                                    <div class="w-20 h-15 bg-gray-200 rounded mr-3 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($post->title, 50) }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($post->excerpt, 60) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($post->category)
                                <span class="px-2 py-1 text-xs rounded-full" style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }}">
                                    {{ $post->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400">Không có</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $post->author->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="status-badge status-{{ $post->status }}">
                                {{ $post->getStatuses()[$post->status] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ number_format($post->views) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $post->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.posts.edit', $post) }}" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                   title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.posts.preview', $post) }}" 
                                   target="_blank"
                                   class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                   title="Xem trước">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="delete-btn p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        data-id="{{ $post->id }}"
                                        title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-newspaper text-4xl mb-3 block"></i>
                            Chưa có bài viết nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($posts->hasPages())
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            {{ $posts->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
    
    <!-- Bulk Actions -->
    <div id="bulk-actions" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg px-6 py-3 hidden">
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600"><span id="selected-count">0</span> mục đã chọn</span>
            <button id="bulk-publish" class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-check mr-2"></i>Xuất bản
            </button>
            <button id="bulk-draft" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-edit mr-2"></i>Chuyển nháp
            </button>
            <button id="bulk-delete" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                <i class="fas fa-trash mr-2"></i>Xóa
            </button>
        </div>
    </div>
</main>

@endsection

@push('scripts')
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Toastr configuration
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };
    
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
            $('#bulk-actions').removeClass('hidden');
            $('#selected-count').text(checkedCount);
        } else {
            $('#bulk-actions').addClass('hidden');
        }
    }
    
    // Delete post
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        const title = row.find('.text-gray-900').first().text();
        
        Swal.fire({
            title: 'Xóa bài viết?',
            html: `Bạn có chắc muốn xóa bài viết "<strong>${title}</strong>"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash mr-2"></i>Xóa',
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
                    toastr.success(response.message);
                    $(`.post-row[data-id="${id}"]`).fadeOut(400, function() {
                        $(this).remove();
                        updateBulkActions();
                    });
                }
            },
            error: function() {
                toastr.error('Có lỗi xảy ra khi xóa bài viết');
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
            toastr.warning('Vui lòng chọn ít nhất một bài viết');
            return;
        }
        
        $.post('/admin/posts/bulk-update', {
            ids: ids,
            action: action
        }, function(response) {
            if (response.success) {
                toastr.success(response.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        }).fail(function() {
            toastr.error('Có lỗi xảy ra khi xử lý');
        });
    }
    
    // Refresh button
    $('#refresh-btn').click(function() {
        $(this).find('i').addClass('fa-spin');
        location.reload();
    });
    
    // Show success/error messages from session
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif
    
    @if(session('error'))
        toastr.error('{{ session('error') }}');
    @endif
});
</script>
@endpush