@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Chi tiết Prompt</h1>
        <div>
            <a href="{{ route('admin.prompts.edit', $prompt) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Chỉnh sửa
            </a>
            <a href="{{ route('admin.prompts.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="h4 mb-3">{{ $prompt->title }}</h2>
                    
                    <div class="mb-4">
                        <strong>Mô tả:</strong>
                        <p>{{ $prompt->description ?? 'Không có mô tả' }}</p>
                    </div>

                    <div class="mb-4">
                        <strong>Nội dung Prompt:</strong>
                        <pre class="bg-light p-3 rounded"><code>{{ $prompt->content }}</code></pre>
                    </div>

                    @if($prompt->example_output)
                    <div class="mb-4">
                        <strong>Ví dụ kết quả:</strong>
                        <pre class="bg-light p-3 rounded"><code>{{ $prompt->example_output }}</code></pre>
                    </div>
                    @endif

                    @if($prompt->variable_descriptions)
                    <div class="mb-4">
                        <strong>Mô tả biến:</strong>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Biến</th>
                                    <th>Mô tả</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prompt->variable_descriptions as $variable => $description)
                                <tr>
                                    <td><code>@{{ '{{' . $variable . '}}' }}</code></td>
                                    <td>{{ $description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h3 class="h5 mb-3">Thông tin chung</h3>
                    
                    <dl class="row mb-0">
                        <dt class="col-sm-4">ID:</dt>
                        <dd class="col-sm-8">{{ $prompt->id }}</dd>

                        <dt class="col-sm-4">Slug:</dt>
                        <dd class="col-sm-8">{{ $prompt->slug }}</dd>

                        <dt class="col-sm-4">Danh mục:</dt>
                        <dd class="col-sm-8">
                            <span class="badge" style="background-color: {{ $prompt->category->color }}">
                                {{ $prompt->category->name }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Người tạo:</dt>
                        <dd class="col-sm-8">{{ $prompt->user->name }}</dd>

                        <dt class="col-sm-4">Trạng thái:</dt>
                        <dd class="col-sm-8">
                            @if($prompt->is_public)
                                <span class="badge bg-success">Công khai</span>
                            @else
                                <span class="badge bg-secondary">Riêng tư</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Lượt xem:</dt>
                        <dd class="col-sm-8">{{ $prompt->view_count }}</dd>

                        <dt class="col-sm-4">Lượt dùng:</dt>
                        <dd class="col-sm-8">{{ $prompt->use_count }}</dd>

                        <dt class="col-sm-4">Ngày tạo:</dt>
                        <dd class="col-sm-8">{{ $prompt->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4">Cập nhật:</dt>
                        <dd class="col-sm-8">{{ $prompt->updated_at->format('d/m/Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

            @if($prompt->tags->count() > 0)
            <div class="card">
                <div class="card-body">
                    <h3 class="h5 mb-3">Tags</h3>
                    <div>
                        @foreach($prompt->tags as $tag)
                            <span class="badge bg-info me-1 mb-1">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection