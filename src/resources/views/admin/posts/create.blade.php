@extends('layouts.admin')

@section('title', 'Viết bài mới')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Quill Editor CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        min-height: 42px;
    }
    
    .ql-editor {
        min-height: 300px;
    }
    
    .image-preview {
        max-width: 300px;
        max-height: 200px;
        object-fit: cover;
        border-radius: 0.5rem;
    }
    
    .char-counter {
        font-size: 0.75rem;
        color: #6b7280;
    }
    
    /* Drag & drop styles */
    .drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .drop-zone:hover {
        border-color: #3b82f6;
        background-color: #eff6ff;
    }
    
    .drop-zone.dragover {
        border-color: #3b82f6;
        background-color: #dbeafe;
    }
</style>
@endpush

<!-- Header -->
@section('header')
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('admin.posts.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-xl font-semibold text-gray-900">Viết bài mới</h1>
            </div>
            <div class="flex items-center space-x-3">
                <button type="button" id="save-draft-btn" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-save mr-2"></i>Lưu nháp
                </button>
                <button type="submit" form="post-form" name="status" value="published" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Xuất bản
                </button>
            </div>
        </div>
    </div>
</header>
@overwrite

@section('content')
<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form id="post-form" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Title -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Tiêu đề bài viết <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('title') border-red-500 @enderror"
                               placeholder="Nhập tiêu đề bài viết..."
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Slug -->
                    <div class="mb-4">
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                            Đường dẫn (URL)
                        </label>
                        <div class="flex items-center">
                            <span class="text-gray-500 mr-2">{{ url('/blog') }}/</span>
                            <input type="text" 
                                   id="slug" 
                                   name="slug"
                                   value="{{ old('slug') }}"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none @error('slug') border-red-500 @enderror"
                                   placeholder="duong-dan-bai-viet">
                        </div>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Excerpt -->
                    <div>
                        <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả ngắn
                        </label>
                        <textarea id="excerpt" 
                                  name="excerpt"
                                  rows="3"
                                  maxlength="500"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none @error('excerpt') border-red-500 @enderror"
                                  placeholder="Mô tả ngắn về bài viết (tối đa 500 ký tự)...">{{ old('excerpt') }}</textarea>
                        <div class="flex justify-between mt-1">
                            <div>
                                @error('excerpt')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <span class="char-counter"><span id="excerpt-count">0</span>/500</span>
                        </div>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nội dung bài viết <span class="text-red-500">*</span>
                    </label>
                    <div id="editor" class="bg-white">{!! old('content') !!}</div>
                    <input type="hidden" id="content" name="content" value="{{ old('content') }}">
                    @error('content')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- SEO -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">SEO & Meta Tags</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Title
                            </label>
                            <input type="text" 
                                   id="meta_title" 
                                   name="meta_title"
                                   value="{{ old('meta_title') }}"
                                   maxlength="60"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                   placeholder="Tiêu đề hiển thị trên Google (tối đa 60 ký tự)">
                            <span class="char-counter float-right"><span id="meta-title-count">0</span>/60</span>
                        </div>
                        
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Description
                            </label>
                            <textarea id="meta_description" 
                                      name="meta_description"
                                      rows="3"
                                      maxlength="160"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none"
                                      placeholder="Mô tả hiển thị trên Google (tối đa 160 ký tự)">{{ old('meta_description') }}</textarea>
                            <span class="char-counter float-right"><span id="meta-desc-count">0</span>/160</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
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
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Bản nháp</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Xuất bản</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                                Ngày xuất bản
                            </label>
                            <input type="datetime-local" 
                                   id="published_at" 
                                   name="published_at"
                                   value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        </div>
                    </div>
                </div>
                
                <!-- Category -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Danh mục</h3>
                    
                    <select id="category_id" 
                            name="category_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <a href="{{ route('admin.categories.create') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">
                        + Thêm danh mục mới
                    </a>
                </div>
                
                <!-- Tags -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tags</h3>
                    
                    <select id="tags" 
                            name="tags[]"
                            multiple
                            class="w-full">
                        @foreach($tags as $tag)
                            <option value="{{ $tag->name }}" {{ in_array($tag->name, old('tags', [])) ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <p class="text-sm text-gray-500 mt-2">Nhập và nhấn Enter để thêm tag mới</p>
                </div>
                
                <!-- Featured Image -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ảnh đại diện</h3>
                    
                    <div class="drop-zone" id="image-drop-zone">
                        <input type="file" 
                               id="featured_image" 
                               name="featured_image"
                               accept="image/*"
                               class="hidden">
                        
                        <div id="image-preview-container" class="hidden mb-4">
                            <img id="image-preview" class="image-preview mx-auto mb-3">
                            <button type="button" id="remove-image" class="text-sm text-red-600 hover:underline">
                                <i class="fas fa-times mr-1"></i>Xóa ảnh
                            </button>
                        </div>
                        
                        <div id="upload-prompt">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-gray-600">Kéo thả ảnh vào đây hoặc click để chọn</p>
                            <p class="text-sm text-gray-500 mt-1">JPG, PNG, GIF (Max: 2MB)</p>
                        </div>
                    </div>
                    
                    @error('featured_image')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </form>
</main>
@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Quill Editor JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
$(document).ready(function() {
    // Initialize Quill editor
    const quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Viết nội dung bài viết...',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['link', 'image', 'video'],
                ['clean']
            ]
        }
    });
    
    // Update hidden input when content changes
    quill.on('text-change', function() {
        $('#content').val(quill.root.innerHTML);
    });
    
    // Initialize Select2 for tags
    $('#tags').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        placeholder: 'Chọn hoặc nhập tags...',
        createTag: function(params) {
            const term = $.trim(params.term);
            if (term === '') {
                return null;
            }
            return {
                id: term,
                text: term,
                newTag: true
            };
        }
    });
    
    // Auto-generate slug from title
    $('#title').on('input', function() {
        const title = $(this).val();
        const slug = convertToSlug(title);
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
    
    // Character counters
    $('#excerpt').on('input', function() {
        $('#excerpt-count').text($(this).val().length);
    });
    
    $('#meta_title').on('input', function() {
        $('#meta-title-count').text($(this).val().length);
    });
    
    $('#meta_description').on('input', function() {
        $('#meta-desc-count').text($(this).val().length);
    });
    
    // Initialize counters
    $('#excerpt-count').text($('#excerpt').val().length);
    $('#meta-title-count').text($('#meta_title').val().length);
    $('#meta-desc-count').text($('#meta_description').val().length);
    
    // Image upload with drag & drop
    const dropZone = $('#image-drop-zone');
    const fileInput = $('#featured_image');
    const previewContainer = $('#image-preview-container');
    const uploadPrompt = $('#upload-prompt');
    const imagePreview = $('#image-preview');
    
    // Click to upload
    dropZone.on('click', function() {
        fileInput.click();
    });
    
    // Drag & drop events
    dropZone.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    });
    
    dropZone.on('dragleave', function() {
        $(this).removeClass('dragover');
    });
    
    dropZone.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            handleImageSelect(files[0]);
        }
    });
    
    // File input change
    fileInput.on('change', function() {
        if (this.files.length > 0) {
            handleImageSelect(this.files[0]);
        }
    });
    
    // Handle image selection
    function handleImageSelect(file) {
        if (!file.type.match('image.*')) {
            alert('Vui lòng chọn file ảnh!');
            return;
        }
        
        if (file.size > 2 * 1024 * 1024) {
            alert('File ảnh không được vượt quá 2MB!');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.attr('src', e.target.result);
            previewContainer.removeClass('hidden');
            uploadPrompt.addClass('hidden');
        };
        reader.readAsDataURL(file);
    }
    
    // Remove image
    $('#remove-image').on('click', function() {
        fileInput.val('');
        imagePreview.attr('src', '');
        previewContainer.addClass('hidden');
        uploadPrompt.removeClass('hidden');
    });
    
    // Save draft
    $('#save-draft-btn').on('click', function() {
        $('#status').val('draft');
        $('#post-form').submit();
    });
    
    // Form validation
    $('#post-form').on('submit', function(e) {
        const title = $('#title').val().trim();
        const content = quill.getText().trim();
        
        if (!title) {
            e.preventDefault();
            alert('Vui lòng nhập tiêu đề bài viết!');
            $('#title').focus();
            return false;
        }
        
        if (content.length < 10) {
            e.preventDefault();
            alert('Nội dung bài viết quá ngắn!');
            return false;
        }
        
        // Update content before submit
        $('#content').val(quill.root.innerHTML);
    });
    
    // Upload image in editor
    quill.getModule('toolbar').addHandler('image', function() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();
        
        input.onchange = function() {
            const file = input.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('image', file);
                
                $.ajax({
                    url: '{{ route("admin.posts.upload-image") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            const range = quill.getSelection();
                            quill.insertEmbed(range.index, 'image', response.url);
                        }
                    },
                    error: function() {
                        alert('Có lỗi xảy ra khi upload ảnh!');
                    }
                });
            }
        };
    });
});
</script>
@endpush