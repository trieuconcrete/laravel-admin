@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Quản lý Prompts</h1>
        <a href="{{ route('admin.prompts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Thêm Prompt mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Danh mục</th>
                            <th>Người tạo</th>
                            <th>Công khai</th>
                            <th>Lượt xem</th>
                            <th>Lượt dùng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prompts as $prompt)
                        <tr>
                            <td>{{ $prompt->id }}</td>
                            <td>
                                <a href="{{ route('admin.prompts.show', $prompt) }}">
                                    {{ $prompt->title }}
                                </a>
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $prompt->category->color }}">
                                    {{ $prompt->category->name }}
                                </span>
                            </td>
                            <td>{{ $prompt->user->name }}</td>
                            <td>
                                <form action="{{ route('admin.prompts.toggle-public', $prompt) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $prompt->is_public ? 'btn-success' : 'btn-secondary' }}">
                                        @if($prompt->is_public)
                                            <i class="bi bi-eye"></i>
                                        @else
                                            <i class="bi bi-eye-slash"></i>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td>{{ $prompt->view_count }}</td>
                            <td>{{ $prompt->use_count }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.prompts.edit', $prompt) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.prompts.destroy', $prompt) }}" method="POST" 
                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa prompt này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $prompts->links() }}
        </div>
    </div>
</div>
@endsection