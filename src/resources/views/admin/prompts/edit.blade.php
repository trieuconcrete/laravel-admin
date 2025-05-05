@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Chỉnh sửa Prompt</h1>
        <a href="{{ route('admin.prompts.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.prompts.update', $prompt) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Tiêu đề *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $prompt->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug', $prompt->slug) }}">
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Mô tả</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $prompt->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Nội dung prompt *</label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="10" required>{{ old('content', $prompt->content) }}</textarea>
                            <small class="text-muted">Sử dụng @{{variable}} để định nghĩa biến</small>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="example_output" class="form-label">Ví dụ kết quả</label>
                            <textarea class="form-control @error('example_output') is-invalid @enderror" 
                                      id="example_output" name="example_output" rows="5">{{ old('example_output', $prompt->example_output) }}</textarea>
                            @error('example_output')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="variable_descriptions" class="form-label">Mô tả biến (JSON)</label>
                            <textarea class="form-control @error('variable_descriptions') is-invalid @enderror" 
                                      id="variable_descriptions" name="variable_descriptions" rows="5">{{ old('variable_descriptions', json_encode($prompt->variable_descriptions)) }}</textarea>
                            <small class="text-muted">Định dạng: {"variable_name": "Mô tả biến"}</small>
                            @error('variable_descriptions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Danh mục *</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $prompt->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                @foreach($tags as $tag)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tags[]" 
                                               value="{{ $tag->id }}" id="tag{{ $tag->id }}"
                                               {{ in_array($tag->id, old('tags', $selectedTags)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tag{{ $tag->id }}">
                                            {{ $tag->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_public" 
                                       name="is_public" value="1" {{ old('is_public', $prompt->is_public) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_public">Công khai</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="mb-1"><strong>Thống kê:</strong></p>
                            <ul class="list-unstyled">
                                <li>Lượt xem: {{ $prompt->view_count }}</li>
                                <li>Lượt sử dụng: {{ $prompt->use_count }}</li>
                                <li>Ngày tạo: {{ $prompt->created_at->format('d/m/Y H:i') }}</li>
                                <li>Cập nhật lần cuối: {{ $prompt->updated_at->format('d/m/Y H:i') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Cập nhật Prompt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-generate slug
document.getElementById('title').addEventListener('input', function() {
    const slugInput = document.getElementById('slug');
    if (!slugInput.value) {
        slugInput.value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }
});

// Variable extractor
function extractVariables() {
    const content = document.getElementById('content').value;
    const variables = content.match(/\{\{([^}]+)\}\}/g);
    
    if (variables) {
        const varNames = variables.map(v => v.replace(/\{\{|\}\}/g, '').trim());
        const uniqueVars = [...new Set(varNames)];
        
        const descriptionsObj = {};
        uniqueVars.forEach(v => {
            descriptionsObj[v] = '';
        });
        
        document.getElementById('variable_descriptions').value = JSON.stringify(descriptionsObj, null, 2);
    }
}

// JSON validator
document.getElementById('variable_descriptions').addEventListener('blur', function() {
    try {
        JSON.parse(this.value);
        this.classList.remove('is-invalid');
    } catch (e) {
        this.classList.add('is-invalid');
    }
});
</script>
@endpush