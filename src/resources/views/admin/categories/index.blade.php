@extends('layouts.admin')

@section('title', 'Quản lý danh mục')

@push('styles')
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- jQuery UI for sortable -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .color-preview {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        display: inline-block;
        vertical-align: middle;
        border: 1px solid #e5e7eb;
    }
    
    .ui-sortable-helper {
        background-color: #f3f4f6;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .sortable-placeholder {
        background-color: #dbeafe;
        visibility: visible !important;
        height: 60px !important;
    }
    
    .handle {
        cursor: move;
    }
</style>
@endpush

@section('content')

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Search -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="relative">
            <input type="text" 
                   id="search-input"
                   placeholder="Tìm kiếm danh mục..." 
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
    </div>
    
    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                        <i class="fas fa-sort"></i>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Danh mục</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Màu sắc</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số bài viết</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody id="categories-tbody" class="divide-y divide-gray-200">
                @forelse($categories as $category)
                <tr class="category-row hover:bg-gray-50" data-id="{{ $category->id }}">
                    <td class="px-6 py-4">
                        <i class="fas fa-grip-vertical handle text-gray-400"></i>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                            @if($category->description)
                                <div class="text-sm text-gray-500">{{ Str::limit($category->description, 50) }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $category->slug }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <span class="color-preview" style="background-color: {{ $category->color }}"></span>
                            <span class="ml-2 text-sm text-gray-600">{{ $category->color }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $category->posts_count }}
                    </td>
                    <td class="px-6 py-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" 
                                   class="sr-only peer status-toggle" 
                                   data-id="{{ $category->id }}"
                                   {{ $category->is_active ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <button class="edit-btn p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                    data-id="{{ $category->id }}"
                                    title="Chỉnh sửa">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="delete-btn p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}"
                                    data-posts="{{ $category->posts_count }}"
                                    title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <i class="fas fa-folder-open text-4xl mb-3 block"></i>
                        Chưa có danh mục nào
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        @if($categories->hasPages())
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</main>

<!-- Category Modal -->
<div id="category-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeCategoryModal()"></div>
    <div class="fixed inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-full p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">Thêm danh mục mới</h3>
                </div>
                
                <form id="category-form" class="p-6">
                    <input type="hidden" id="category-id">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Tên danh mục <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                   required>
                            <span class="text-sm text-red-500 hidden" id="name-error"></span>
                        </div>
                        
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                                Slug
                            </label>
                            <input type="text" 
                                   id="slug" 
                                   name="slug"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                   placeholder="tu-dong-tao-tu-ten">
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Mô tả
                            </label>
                            <textarea id="description" 
                                      name="description"
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none"></textarea>
                        </div>
                        
                        <div>
                            <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                                Màu sắc <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center space-x-2">
                                <input type="color" 
                                       id="color" 
                                       name="color"
                                       value="#3B82F6"
                                       class="h-10 w-20">
                                <input type="text" 
                                       id="color-text" 
                                       value="#3B82F6"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                       pattern="^#[0-9A-Fa-f]{6}$"
                                       maxlength="7">
                            </div>
                        </div>
                        
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-1">
                                Thứ tự
                            </label>
                            <input type="number" 
                                   id="order" 
                                   name="order"
                                   min="0"
                                   value="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active"
                                       checked
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                <span class="text-sm text-gray-700">Kích hoạt</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <button type="button" 
                                onclick="closeCategoryModal()"
                                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                            Hủy
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i>Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
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
    
    // Make table sortable
    $('#categories-tbody').sortable({
        handle: '.handle',
        placeholder: 'sortable-placeholder',
        update: function(event, ui) {
            updateOrder();
        }
    });
    
    // Update order
    function updateOrder() {
        const categories = [];
        $('#categories-tbody tr').each(function(index) {
            categories.push({
                id: $(this).data('id'),
                order: index
            });
        });
        
        $.ajax({
            url: '{{ route("admin.categories.update-order") }}',
            method: 'POST',
            data: { categories: categories },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                }
            }
        });
    }
    
    // Search functionality
    $('#search-input').on('input', function() {
        const search = $(this).val().toLowerCase();
        $('#categories-tbody tr').each(function() {
            const name = $(this).find('.text-gray-900').text().toLowerCase();
            const description = $(this).find('.text-gray-500').text().toLowerCase();
            
            if (name.includes(search) || description.includes(search)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Open create modal
    $('#create-category-btn').click(function() {
        resetForm();
        $('#modal-title').text('Thêm danh mục mới');
        $('#category-modal').removeClass('hidden');
    });
    
    // Edit category
    $(document).on('click', '.edit-btn', function() {
        const id = $(this).data('id');
        
        $.get(`/admin/categories/${id}/edit`, function(response) {
            $('#modal-title').text('Chỉnh sửa danh mục');
            $('#category-id').val(response.id);
            $('#name').val(response.name);
            $('#slug').val(response.slug);
            $('#description').val(response.description);
            $('#color').val(response.color);
            $('#color-text').val(response.color);
            $('#order').val(response.order);
            $('#is_active').prop('checked', response.is_active);
            
            $('#category-modal').removeClass('hidden');
        });
    });
    
    // Close modal
    window.closeCategoryModal = function() {
        $('#category-modal').addClass('hidden');
        resetForm();
    }
    
    // Reset form
    function resetForm() {
        $('#category-form')[0].reset();
        $('#category-id').val('');
        $('#color').val('#3B82F6');
        $('#color-text').val('#3B82F6');
        $('.text-red-500').addClass('hidden');
        $('input, textarea').removeClass('border-red-500');
    }
    
    // Auto-generate slug
    $('#name').on('input', function() {
        const name = $(this).val();
        const slug = convertToSlug(name);
        $('#slug').val(slug);
    });
    
    function convertToSlug(text) {
        return text
            .toLowerCase()
            .replace(/[áàảãạăắằẳẵặâấầẩẫậ]/g, 'a')
            .replace(/[éèẻẽẹêếềểễệ]/g, 'e')
            .replace(/[íìỉĩị]/g, 'i')
            .replace(/[óòỏõọôốồổỗộơớờởỡợ]/g, 'o')
            .replace(/[úùủũụưứừửữự]/g, 'u')
            .replace(/[ýỳỷỹỵ]/g, 'y')
            .replace(/đ/g, 'd')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }
    
    // Sync color inputs
    $('#color').on('input', function() {
        $('#color-text').val($(this).val());
    });
    
    $('#color-text').on('input', function() {
        const value = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
            $('#color').val(value);
        }
    });
    
    // Submit form
    $('#category-form').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#category-id').val();
        const url = id ? `/admin/categories/${id}` : '/admin/categories';
        const method = id ? 'PUT' : 'POST';
        
        const data = {
            name: $('#name').val(),
            slug: $('#slug').val(),
            description: $('#description').val(),
            color: $('#color').val(),
            order: $('#order').val() || 0,
            is_active: $('#is_active').is(':checked') ? 1 : 0
        };
        
        $.ajax({
            url: url,
            method: method,
            data: data,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    closeCategoryModal();
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        $(`#${key}`).addClass('border-red-500');
                        $(`#${key}-error`).text(errors[key][0]).removeClass('hidden');
                    });
                } else {
                    toastr.error('Có lỗi xảy ra!');
                }
            }
        });
    });
    
    // Toggle status
    $(document).on('change', '.status-toggle', function() {
        const id = $(this).data('id');
        
        $.post(`/admin/categories/${id}/toggle-status`, {
            _token: '{{ csrf_token() }}'
        }, function(response) {
            if (response.success) {
                toastr.success(response.message);
            }
        });
    });
    
    // Delete category
    $(document).on('click', '.delete-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const posts = $(this).data('posts');
        
        if (posts > 0) {
            Swal.fire({
                title: 'Không thể xóa!',
                html: `Danh mục "<strong>${name}</strong>" đang có ${posts} bài viết.<br>Vui lòng chuyển các bài viết sang danh mục khác trước khi xóa.`,
                icon: 'warning',
                confirmButtonText: 'Đã hiểu'
            });
            return;
        }
        
        Swal.fire({
            title: 'Xóa danh mục?',
            html: `Bạn có chắc muốn xóa danh mục "<strong>${name}</strong>"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash mr-2"></i>Xóa',
            cancelButtonText: 'Hủy',
            confirmButtonColor: '#ef4444',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                deleteCategory(id);
            }
        });
    });
    
    function deleteCategory(id) {
        $.ajax({
            url: `/admin/categories/${id}`,
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $(`.category-row[data-id="${id}"]`).fadeOut(400, function() {
                        $(this).remove();
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                toastr.error(response.message || 'Có lỗi xảy ra!');
            }
        });
    }
    
    // Show messages from session
    @if(session('success'))
        toastr.success('{{ session('success') }}');
    @endif
});
</script>
@endpush