@extends('admin.layout')
@section('title', 'Tạo bài viết mới')

@push('styles')
    <style>
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

        .ql-editor {
            min-height: 300px;
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

        .char-counter {
            font-size: 0.75rem;
            color: #6b7280;
        }

        #status {
            width: unset;
            height: unset;
            position: unset;
            left: unset;
            top: unset;
            margin: unset;
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

@section('content')
    <main class="container-fluid">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.posts.index') }}" class="btn-outline-primary me-3 btn">
                    Danh sách bài viết
                </a>
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="preview-btn" class="btn btn-outline-primary">Xem trước</button>
                <button type="submit" form="post-form" name="status" value="published" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Xuất bản
                </button>
            </div>
        </div>

        <form id="post-form" action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row g-4">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Title -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Tiêu đề bài viết <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="Nhập tiêu đề bài viết..." required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div class="mb-3">
                                <label for="slug" class="form-label">Đường dẫn (URL)</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ url('/blog') }}/</span>
                                    <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                                        class="form-control @error('slug') is-invalid @enderror"
                                        placeholder="duong-dan-bai-viet">
                                </div>
                                @error('slug')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Excerpt -->
                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Mô tả ngắn</label>
                                <textarea id="excerpt" name="excerpt" rows="3" maxlength="500"
                                    class="form-control @error('excerpt') is-invalid @enderror"
                                    placeholder="Mô tả ngắn về bài viết (tối đa 500 ký tự)...">{{ old('excerpt') }}</textarea>
                                <div class="d-flex justify-content-between small mt-1">
                                    @error('excerpt')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <span class="text-muted"><span id="excerpt-count">0</span>/500</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <label class="form-label">Nội dung bài viết <span class="text-danger">*</span></label>
                            <div id="editor" class="border rounded p-2">{!! old('content') !!}</div>
                            <input type="hidden" id="content" name="content" value="{{ old('content') }}">
                            @error('content')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">SEO & Meta Tags</h5>

                            <div class="mb-3">
                                <label for="meta_title" class="form-label">Meta Title</label>
                                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title') }}"
                                    maxlength="60" class="form-control"
                                    placeholder="Tiêu đề hiển thị trên Google (tối đa 60 ký tự)">
                                <div class="text-end small text-muted mt-1"><span id="meta-title-count">0</span>/60</div>
                            </div>

                            <div class="mb-3">
                                <label for="meta_description" class="form-label">Meta Description</label>
                                <textarea id="meta_description" name="meta_description" rows="3" maxlength="160" class="form-control"
                                    placeholder="Mô tả hiển thị trên Google (tối đa 160 ký tự)">{{ old('meta_description') }}</textarea>
                                <div class="text-end small text-muted mt-1"><span id="meta-desc-count">0</span>/160</div>
                            </div>

                            <div class="mb-3">
                                <label for="meta_tags" class="form-label">Meta Keywords</label>
                                <input type="text" id="meta_tags" name="meta_tags"
                                    value="{{ old('meta_tags') ? implode(',', old('meta_tags')) : '' }}"
                                    class="form-control" placeholder="Nhập từ khóa và nhấn Enter">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Publish Settings -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Cài đặt xuất bản</h5>
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select id="status" name="status" class="form-select">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Bản nháp
                                    </option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Xuất
                                        bản</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="published_at" class="form-label">Ngày xuất bản</label>
                                <input type="datetime-local" id="published_at" name="published_at"
                                    value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}" class="form-control">
                            </div>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Danh mục</h5>
                            <select id="category_id" name="category_id" class="form-select mb-2">
                                <option value="">Chọn danh mục</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                    <div class="card mb-4">
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
                            <label id="image-drop-zone" class="w-100 border rounded p-3 mb-0" style="cursor: pointer;">
                                <input type="file" id="featured_image" name="featured_image" accept="image/*"
                                    class="d-none">

                                <div id="image-preview-container" class="d-none">
                                    <img id="image-preview" class="mb-2">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <button type="button" id="remove-image" class="btn btn-sm btn-link text-danger">
                                            Xóa ảnh
                                        </button>
                                    </div>

                                </div>

                                <div id="upload-prompt">
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

    <x-toast-notification />
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
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
                                    showToast('success', result.error || 'Upload failed');
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
                            $("#category_id").append(
                                `<option value="${response.category.id}" selected>${response.category.name}</option>`
                            );

                            $("#addCategoryModal").modal("hide");
                            form[0].reset();

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

                            $("#tags").append(
                                `<option value="${response.tag.id}" selected>${response.tag.name}</option>`
                            );


                            $("#addTagModal").modal("hide");
                            form[0].reset();


                            showToast('success', response.message);
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        showToast('error', Object.values(errors).join(", "));
                    }
                });
            });

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

            $('#title').on('input', function() {
                const title = $(this).val();
                const slug = slugify(title);
                $('#slug').val(slug);
            });

            $('#excerpt').on('input', function() {
                $('#excerpt-count').text($(this).val().length);
            });

            $('#meta_title').on('input', function() {
                $('#meta-title-count').text($(this).val().length);
            });

            $('#meta_description').on('input', function() {
                $('#meta-desc-count').text($(this).val().length);
            });

            $('#excerpt-count').text($('#excerpt').val().length);
            $('#meta-title-count').text($('#meta_title').val().length);
            $('#meta-desc-count').text($('#meta_description').val().length);

            $('#preview-btn').on('click', function() {
                const title = $('#title').val().trim();
                const content = tinymce.get('content').getContent({
                    format: 'text'
                }).trim();

                if (!title) {
                    showToast("error", "Vui lòng nhập tiêu đề bài viết!");
                    return;
                }

                if (!content) {
                    showToast("error", "Vui lòng nhập nội dung!");
                    return;
                }

                if (content.length < 10) {
                    showToast("error", "Nội dung bài viết quá ngắn!");
                    return;
                }

                $('#status').val('draft');
                $('#content').val(tinymce.get('content').getContent());

                let form = $('#post-form')[0];
                let formData = new FormData(form);

                $.ajax({
                    url: $('#post-form').attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success && response.post) {
                            window.location.href = `/admin/posts/${response.post.id}/preview/`;
                        } else {
                            showToast("error", "Lỗi khi lưu");
                        }
                    },
                    error: function(xhr) {
                        let message = xhr.responseJSON?.message || "Có lỗi xảy ra!";
                        showToast("error", message);
                    }
                });
            });

            const dropZone = $('#image-drop-zone');
            const inputFile = $('#featured_image');
            const previewContainer = $('#image-preview-container');
            const previewImage = $('#image-preview');
            const removeButton = $('#remove-image');
            const uploadPrompt = $('#upload-prompt');

            function preview(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.attr('src', e.target.result);
                    previewContainer.removeClass('d-none');
                    uploadPrompt.addClass('d-none');
                };
                reader.readAsDataURL(file);
            }

            function resetPreview() {
                inputFile.val('');
                previewImage.attr('src', '');
                previewContainer.addClass('d-none');
                uploadPrompt.removeClass('d-none');
            }

            inputFile.on('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    alert('Vui lòng chọn đúng định dạng ảnh.');
                    resetPreview();
                    return;
                }

                if (file.size > 2 * 1024 * 1024) {
                    alert('Ảnh quá lớn. Giới hạn là 2MB.');
                    resetPreview();
                    return;
                }

                preview(file);
            });

            removeButton.on('click', function() {
                resetPreview();
            });

            dropZone.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.addClass('ring-2 ring-blue-500');
            });

            dropZone.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.removeClass('ring-2 ring-blue-500');
            });

            dropZone.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropZone.removeClass('ring-2 ring-blue-500');

                const files = e.originalEvent.dataTransfer.files;
                if (files.length > 0) {
                    inputFile[0].files = files;
                    inputFile.trigger('change');
                }
            });

            dropZone.on('click', function() {
                inputFile.trigger('click');
            });

            $('#save-draft-btn').on('click', function() {
                $('#status').val('draft');
                $('#post-form').submit();
            });

            $('#post-form').on('submit', function(e) {
                const title = $('#title').val().trim();
                const content = tinymce.get('content').getContent({
                    format: 'text'
                }).trim();

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

                $('#content').val(tinymce.get('content').getContent());
            });
        });
    </script>
@endpush
