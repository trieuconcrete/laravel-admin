@extends('layouts.admin')

@section('title', 'Chỉnh sửa bài viết')

@push('styles')
<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .preview-container {
        position: relative;
        width: 100%;
        height: 200px;
        background: #f3f4f6;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .preview-container .placeholder {
        text-align: center;
        color: #6b7280;
    }
    
    .preview-container .remove-image {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        display: none;
    }
    
    .preview-container:hover .remove-image {
        display: block;
    }
    
    /* Select2 custom styles */
    .select2-container--default .select2-selection--multiple {
        border-color: #d1d5db;
        min-height: 42px;
        padding: 4px 8px;
    }
    
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3b82f6;
        outline: 2px solid transparent;
        outline-offset: 2px;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .post-info {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 14px;
        color: #6b7280;
    }
</style>
@endpush

@section('header')
<!-- Header -->
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('admin.posts.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-xl font-semibold text-gray-900">Chỉnh sửa bài viết</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.posts.preview', $post) }}" 
                   target="_blank"
                   class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-eye mr-2"></i>Xem trước
                </a>
                <button type="button" id="update-post" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>Cập nhật
                </button>
            </div>
        </div>
    </div>
</header>
@overwrite

@section('content')
<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form id="post-form" action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Post Info -->
                <div class="post-info">
                    <i class="fas fa-info-circle mr-2"></i>
                    Tạo bởi <strong>{{ $post->author->name }}</strong> vào {{ $post->created_at->format('d/m/Y H:i') }}
                    @if($post->updated_at != $post->created_at)
                        • Cập nhật lần cuối: {{ $post->updated_at->format('d/m/Y H:i') }}
                    @endif
                    • {{ $post->views }} lượt xem
                </div>
                
                <!-- Title -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Tiêu đề bài viết <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $post->title) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('title') border-red-500 @enderror"
                               placeholder="Nhập tiêu đề bài viết..."
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                            Đường dẫn (URL)
                        </label>
                        <div class="flex items-center">
                            <span class="text-gray-500 mr-2">{{ url('/blog') }}/</span>
                            <input type="text" 
                                   id="slug" 
                                   name="slug" 
                                   value="{{ old('slug', $post->slug) }}"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('slug') border-red-500 @enderror"
                                   placeholder="tu-dong-tao-tu-tieu-de">
                        </div>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả ngắn
                        </label>
                        <textarea id="excerpt" 
                                  name="excerpt" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('excerpt') border-red-500 @enderror"
                                  placeholder="Mô tả ngắn về bài viết (tối đa 500 ký tự)..."
                                  maxlength="500">{{ old('excerpt', $post->excerpt) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">
                            <span id="excerpt-count">0</span>/500 ký tự
                        </p>
                        @error('excerpt')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Content -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Nội dung bài viết <span class="text-red-500">*</span>
                    </label>
                    <textarea id="content" name="content" required>{{ old('content', $post->content) }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- SEO -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-search mr-2"></i>Tối ưu SEO
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Title
                            </label>
                            <input type="text" 
                                   id="meta_title" 
                                   name="meta_title" 
                                   value="{{ old('meta_title', $post->meta_title) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                   placeholder="Để trống sẽ lấy từ tiêu đề bài viết">
                            <p class="mt-1 text-sm text-gray-500">Tối ưu: 50-60 ký tự</p>
                        </div>
                        
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Description
                            </label>
                            <textarea id="meta_description" 
                                      name="meta_description" 
                                      rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                      placeholder="Để trống sẽ lấy từ mô tả ngắn">{{ old('meta_description', $post->meta_description) }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Tối ưu: 150-160 ký tự</p>
                        </div>
                        
                        <div>
                            <label for="meta_tags" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Keywords
                            </label>
                            <input type="text" 
                                   id="meta_tags" 
                                   name="meta_tags[]" 
                                   value="{{ old('meta_tags', $post->meta_tags) ? implode(',', old('meta_tags', $post->meta_tags)) : '' }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                   placeholder="Nhập từ khóa và nhấn Enter">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Publish Settings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cài đặt xuất bản</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Trạng thái
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                                <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Xuất bản</option>
                                <option value="archived" {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>Lưu trữ</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Ngày xuất bản
                            </label>
                            <input type="datetime-local" 
                                   id="published_at" 
                                   name="published_at" 
                                   value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                            <p class="mt-1 text-sm text-gray-500">Để trống sẽ xuất bản ngay</p>
                        </div>
                    </div>
                </div>
                
                <!-- Featured Image -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ảnh đại diện</h3>
                    
                    <div class="preview-container" id="image-preview">
                        @if($post->featured_image)
                            <img src="{{ $post->featured_image_url }}" alt="Featured image">
                            <button type="button" class="remove-image" onclick="removeImage()">
                                <i class="fas fa-times mr-1"></i>Xóa ảnh
                            </button>
                        @else
                            <div class="placeholder">
                                <i class="fas fa-image text-4xl mb-2"></i>
                                <p>Chưa có ảnh</p>
                            </div>
                            <button type="button" class="remove-image" onclick="removeImage()">
                                <i class="fas fa-times mr-1"></i>Xóa ảnh
                            </button>
                        @endif
                    </div>
                    
                    <input type="file" 
                           id="featured_image" 
                           name="featured_image" 
                           accept="image/*"
                           class="hidden">
                    
                    <button type="button" 
                            onclick="document.getElementById('featured_image').click()"
                            class="mt-4 w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Chọn ảnh mới
                    </button>
                    
                    <p class="mt-2 text-sm text-gray-500">
                        JPG, PNG, GIF. Tối đa 2MB
                    </p>
                </div>
                
                <!-- Category & Tags -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Phân loại</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Danh mục
                            </label>
                            <select id="category_id" 
                                    name="category_id" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                                Thẻ (Tags)
                            </label>
                            <select id="tags" 
                                    name="tags[]" 
                                    multiple
                                    class="w-full">
                                @php
                                    $selectedTags = old('tags', $post->tags->pluck('name')->toArray());
                                @endphp
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->name }}" 
                                            {{ in_array($tag->name, $selectedTags) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">
                                Nhập để tạo thẻ mới
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</main>
@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize TinyMCE
    tinymce.init({
        selector: '#content',
        height: 500,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
        images_upload_url: '{{ route("admin.posts.upload-image") }}',
        images_upload_handler: function (blobInfo, success, failure) {
            var xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '{{ route("admin.posts.upload-image") }}');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            
            xhr.onload = function() {
                var json;
                if (xhr.status != 200) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }
                json = JSON.parse(xhr.responseText);
                if (!json || typeof json.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                success(json.location);
            };
            
            formData = new FormData();
            formData.append('image', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        }
    });
    
    // Initialize Select2 for tags
    $('#tags').select2({
        tags: true,
        tokenSeparators: [','],
        placeholder: 'Chọn hoặc nhập thẻ mới...',
        createTag: function (params) {
            var term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            }
        }
    });
    
    // Character counter
    $('#excerpt').on('input', function() {
        $('#excerpt-count').text($(this).val().length);
    }).trigger('input');
    
    // Image preview
    $('#featured_image').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').html(`
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="remove-image" onclick="removeImage()">
                        <i class="fas fa-times mr-1"></i>Xóa ảnh
                    </button>
                `);
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Update post
    $('#update-post').click(function() {
        $('#post-form').submit();
    });
});

// Remove image
function removeImage() {
    $('#featured_image').val('');
    $('#image-preview').html(`
        <div class="placeholder">
            <i class="fas fa-image text-4xl mb-2"></i>
            <p>Chưa có ảnh</p>
        </div>
        <button type="button" class="remove-image" onclick="removeImage()">
            <i class="fas fa-times mr-1"></i>Xóa ảnh
        </button>
    `);
}
</script>
@endpush