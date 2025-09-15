@extends('admin.layout')
@section('title', 'Chỉnh sửa bài viết')

@push('styles')
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        #status {
            width: unset;
            height: unset;
            position: unset;
            left: unset;
            top: unset;
            margin: unset;
        }

        #generate-slug {
            cursor: pointer;
        }

        #upload-prompt {
            min-height: 150px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        #image-preview {
            width: 100%;
            aspect-ratio: 3/2;
            object-fit: cover;
            border-radius: 0.5rem;
        }

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

        /* Select2 custom styles */
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            padding: 0 0.75rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #F2F2F2;
            color: #000000;
            border: none padding: 2px 4px
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #000000;
            padding: 0 4px
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

@section('content')
    <!-- Main Content -->
    <main>
        <div class="container-fluid">
            <div class="d-flex justify-content-between mb-4 align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.posts.index') }}" class="btn-outline-primary me-3 btn">
                        Danh sách bài viết
                    </a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.posts.preview', ['post' => $post->id]) }}" class="btn-outline-primary me-3 btn">
                        Xem trước
                    </a>
                    <button type="button" id="update-post" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Cập nhật
                    </button>
                </div>
            </div>

            <form id="post-form" action="{{ route('admin.posts.update', $post) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Main Content -->
                    <div class="col-lg-8">
                        <!-- Title -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <!-- Post Info -->
                                <div class="mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Tạo bởi <strong>{{ $post->author->full_name }}</strong> vào
                                    {{ $post->created_at->format('d/m/Y H:i') }}
                                    @if ($post->updated_at != $post->created_at)
                                        • Cập nhật lần cuối: {{ $post->updated_at->format('d/m/Y H:i') }}
                                    @endif
                                    • {{ $post->views }} lượt xem
                                </div>
                                <div class="mb-3">
                                    <label for="title" class="form-label">Tiêu đề bài viết <span
                                            class="text-danger">*</span></label>
                                    <input type="text" id="title" name="title"
                                        value="{{ old('title', $post->title) }}"
                                        class="form-control @error('title') is-invalid @enderror"
                                        placeholder="Nhập tiêu đề bài viết..." required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between">
                                        <label for="slug" class="form-label">Đường dẫn (URL)</label>
                                        <span id="generate-slug">Tạo lại slug</span>
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-text">{{ url('/blog') }}/</span>
                                        <input type="text" id="slug" name="slug"
                                            value="{{ old('slug', $post->slug) }}"
                                            class="form-control @error('slug') is-invalid @enderror"
                                            placeholder="tu-dong-tao-tu-tieu-de">
                                    </div>
                                    @error('slug')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Mô tả ngắn</label>
                                    <textarea id="excerpt" name="excerpt" rows="3" maxlength="500"
                                        class="form-control @error('excerpt') is-invalid @enderror" placeholder="Mô tả ngắn về bài viết...">{{ old('excerpt', $post->excerpt) }}</textarea>
                                    <div class="form-text"><span id="excerpt-count">0</span>/500 ký tự</div>
                                    @error('excerpt')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <label for="content" class="form-label">Nội dung bài viết <span
                                        class="text-danger">*</span></label>
                                <textarea id="content" name="content" class="form-control" required>{{ old('content', $post->content) }}</textarea>
                                @error('content')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- SEO -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-search me-1"></i>Tối ưu SEO</h5>

                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" id="meta_title" name="meta_title"
                                        value="{{ old('meta_title', $post->meta_title) }}" class="form-control"
                                        placeholder="Để trống sẽ lấy từ tiêu đề bài viết">
                                    <div class="form-text">Tối ưu: 50-60 ký tự</div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea id="meta_description" name="meta_description" rows="3" class="form-control"
                                        placeholder="Để trống sẽ lấy từ mô tả ngắn">{{ old('meta_description', $post->meta_description) }}</textarea>
                                    <div class="form-text">Tối ưu: 150-160 ký tự</div>
                                </div>

                                <div class="mb-3">
                                    <label for="meta_tags" class="form-label">Meta Keywords</label>
                                    <input type="text" id="meta_tags" name="meta_tags"
                                        value="{{ is_array(old('meta_tags', $post->meta_tags ?? [])) ? implode(',', old('meta_tags', $post->meta_tags ?? [])) : old('meta_tags', $post->meta_tags ?? '') }}"
                                        class="form-control" placeholder="Nhập từ khóa và nhấn Enter">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Publish Settings -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Cài đặt xuất bản</h5>

                                <div class="mb-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select id="status" name="status" class="form-select">
                                        <option value="draft"
                                            {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Bản nháp
                                        </option>
                                        <option value="published"
                                            {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Xuất bản
                                        </option>
                                        <option value="archived"
                                            {{ old('status', $post->status) == 'archived' ? 'selected' : '' }}>Lưu trữ
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="published_at" class="form-label">Ngày xuất bản</label>
                                    <input type="datetime-local" id="published_at" name="published_at"
                                        value="{{ old('published_at', $post->published_at ? $post->published_at->format('Y-m-d\TH:i') : '') }}"
                                        class="form-control">
                                    <div class="form-text">Để trống sẽ xuất bản ngay</div>
                                </div>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <label for="category_id" class="form-label">Danh mục</label>
                                <select id="category_id" name="category_id" class="form-select  mb-2">
                                    <option value="">Chọn danh mục</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#addCategoryModal">
                                    + Thêm danh mục mới
                                </button>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <label for="tags">Tags</label>
                                <select name="tags[]" id="tags" class="form-control" multiple>
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->id }}"
                                            {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="help-block mt-1 mb-1">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều.</p>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#addTagModal">
                                    + Thêm thẻ mới
                                </button>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Ảnh đại diện</h5>
                                <label id="image-drop-zone" class="w-100 border rounded p-3 mb-0"
                                    style="cursor: pointer;">
                                    <input type="file" id="featured_image" name="featured_image" accept="image/*"
                                        class="d-none">

                                    {{-- Preview nếu có ảnh cũ --}}
                                    <div id="image-preview-container"
                                        class="{{ $post->featured_image ? '' : 'd-none' }}">
                                        <img id="image-preview" class="mb-2 img-fluid"
                                            src="{{ $post->featured_image ? $post->featured_image_url : '' }}"
                                            alt="Ảnh đại diện">

                                        <div class="d-flex justify-content-center align-items-center">
                                            <button type="button" id="remove-image"
                                                class="btn btn-sm btn-link text-danger">
                                                Xóa ảnh
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Prompt khi chưa có ảnh --}}
                                    <div id="upload-prompt"
                                        class="{{ $post->featured_image ? 'd-none' : 'text-center' }}">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                        <p class="mb-1">Kéo thả ảnh vào đây hoặc click để chọn</p>
                                        <small class="text-muted">JPG, PNG, GIF (Max: 2MB)</small>
                                    </div>
                                </label>

                                @error('featured_image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Modal: Add Category -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="category-form" method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCategoryLabel">Thêm danh mục mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="cat-name" class="form-label">Tên danh mục <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="cat-name" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="cat-slug" class="form-label">Slug</label>
                            <input type="text" id="cat-slug" name="slug" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label for="cat-description" class="form-label">Mô tả</label>
                            <textarea id="cat-description" name="description" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="cat-color" class="form-label">Màu sắc <span class="text-danger">*</span></label>
                            <input type="color" id="cat-color" name="color" value="#000000"
                                class="form-control form-control-color">
                        </div>

                        <div class="mb-3">
                            <label for="cat-order" class="form-label">Thứ tự</label>
                            <input type="number" id="cat-order" name="order" class="form-control" min="0"
                                value="0">
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cat-active" name="is_active"
                                value="1" checked>
                            <label class="form-check-label" for="cat-active">Kích hoạt</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Add Tag -->
    <div class="modal fade" id="addTagModal" tabindex="-1" aria-labelledby="addTagLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="tag-form" method="POST" action="{{ route('admin.tags.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addTagLabel">Thêm thẻ mới</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="tag-name" class="form-label">Tên thẻ <span class="text-danger">*</span></label>
                            <input type="text" id="tag-name" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="tag-slug" class="form-label">Slug</label>
                            <input type="text" id="tag-slug" name="slug" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize TinyMCE
            tinymce.init({
                selector: '#content',
                height: 500,
                plugins: 'image code link lists table',
                toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | image link code',
                automatic_uploads: true,
                images_upload_credentials: true,
                relative_urls: false,
                remove_script_host: false,
                document_base_url: '{{ url('/') }}/',
                image_title: true,
                file_picker_types: 'image',
                file_picker_callback: function(callback, value, meta) {
                    if (meta.filetype === 'image') {
                        const input = document.createElement('input');
                        input.setAttribute('type', 'file');
                        input.setAttribute('accept', 'image/*');

                        input.onchange = async function() {
                            const file = this.files[0];
                            const formData = new FormData();
                            formData.append('image', file);

                            try {
                                const response = await fetch(
                                    '{{ route('admin.posts.upload-image') }}', {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    });

                                const result = await response.json();

                                if (result.url) {
                                    callback(result.url, {
                                        title: file.name
                                    });
                                } else {
                                    alert(result.error || 'Upload failed');
                                }
                            } catch (error) {
                                alert('Network error: ' + error.message);
                            }
                        };

                        input.click();
                    }
                },
                image_uploadtab: false,
            });

            $('#generate-slug').on('click', function() {
                let title = $('#title').val();
                let slug = slugify(title);
                $('#slug').val(slug);
            });

            $("#cat-name").on("input", function() {
                let name = $(this).val();
                let slug = slugify(name);

                $("#cat-slug").val(slug);
            });

            $("#category-form").on("submit", function(e) {
                e.preventDefault();
                let form = $(this);

                $.ajax({
                    url: form.attr("action"),
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Thêm option vào select
                            $("#category_id").append(
                                `<option value="${response.category.id}" selected>${response.category.name}</option>`
                            );

                            // Đóng modal
                            $("#addCategoryModal").modal("hide");
                            form[0].reset();

                            // Thông báo
                            showToast('success', response.message);
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        showToast('error', Object.values(errors).join(", "));
                    }
                });
            });

            $("#tag-name").on("input", function() {
                let name = $(this).val();
                let slug = slugify(name);

                $("#tag-slug").val(slug);
            });

            $("#tag-form").on("submit", function(e) {
                e.preventDefault();
                let form = $(this);

                $.ajax({
                    url: form.attr("action"),
                    method: "POST",
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Append new tag to select and select it
                            $("#tags").append(
                                `<option value="${response.tag.id}" selected>${response.tag.name}</option>`
                            );

                            // Close modal
                            $("#addTagModal").modal("hide");
                            form[0].reset();

                            // Show success toast
                            showToast('success', response.message);
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        showToast('error', Object.values(errors).join(", "));
                    }
                });
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
        const fileInput = $('#featured_image');
        const dropZone = $('#image-drop-zone');
        const previewContainer = $('#image-preview-container');
        const previewImage = $('#image-preview');
        const uploadPrompt = $('#upload-prompt');
        const removeBtn = $('#remove-image');

        // Handle preview
        function previewFile(file) {
            if (!file.type.startsWith('image/')) {
                alert('Vui lòng chọn file hình ảnh!');
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.attr('src', e.target.result);
                previewContainer.removeClass('d-none');
                uploadPrompt.addClass('d-none');
            };
            reader.readAsDataURL(file);
        }

        // Upload image
        fileInput.on('change', function() {
            const file = this.files[0];
            if (file) previewFile(file);
        });

        // Remove image
        removeBtn.on('click', function(e) {
            e.preventDefault();
            fileInput.val('');
            previewImage.attr('src', '');
            previewContainer.addClass('d-none');
            uploadPrompt.removeClass('d-none');
        });

        // --- Drag & Drop ---
        dropZone.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.addClass('border-primary bg-light');
        });

        dropZone.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.removeClass('border-primary bg-light');
        });

        dropZone.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.removeClass('border-primary bg-light');

            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                fileInput[0].files = files;
                previewFile(files[0]);
            }
        });
    </script>
@endpush
