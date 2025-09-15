@extends('admin.layout')
@section('title', 'Xem trước bài viết')

@push('styles')
    <style>
        .post-preview-card {
            padding: 16px;
        }

        .post-header img {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 8px;
        }

        .post-meta span {
            font-size: 13px;
            color: #6b7280;
            margin-right: 12px;
        }

        .post-status {
            font-size: 12px;
            font-weight: 500;
            border-radius: 4px;
            padding: 2px 6px;
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

        .post-category {
            font-size: 12px;
            font-weight: 500;
            border-radius: 4px;
            padding: 2px 6px;
        }

        .post-content img {
            max-width: 100%;
            height: auto;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col">

                <!-- Header Buttons -->
                <div class="row mb-3 pb-1">
                    <div class="col-12 d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="me-3 btn btn-outline-primary ">
                            <i class="ri-arrow-left-line align-middle me-1"></i> Quay lại
                        </a>
                        <div>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-primary">
                                <i class="ri-edit-line align-middle me-1"></i> Chỉnh sửa bài viết
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Post Preview Card -->
                <div class="card post-preview-card">
                    <div class="card-body">
                        <!-- Post Header -->
                        <div class="post-header mb-4">
                            <h2 class="fw-bold mb-2">{{ $post->title }}</h2>
                            <div class="post-meta mb-3">
                                <span><i class="ri-user-line me-1"></i> {{ $post->author->full_name ?? 'Không rõ' }}</span>
                                <span><i class="ri-calendar-line me-1"></i> {{ $post->created_at->format('d/m/Y') }}</span>
                                <span><i class="ri-eye-line me-1"></i> {{ number_format($post->views) }} lượt xem</span>
                                @if ($post->category)
                                    <span class="post-category"
                                        style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }}">
                                        {{ $post->category->name }}
                                    </span>
                                @endif
                                <span class="post-status {{ 'status-' . $post->status }}">
                                    {{ $post->getStatuses()[$post->status] }}
                                </span>
                            </div>

                            @if ($post->featured_image)
                                <img src="{{ $post->featured_image_url }}" alt="{{ $post->title }}">
                            @endif
                        </div>

                        <!-- Post Excerpt -->
                        @if ($post->excerpt)
                            <p class="text-muted fst-italic mb-4">{{ $post->excerpt }}</p>
                        @endif

                        <!-- Post Content -->
                        <div class="post-content">
                            {!! $post->content !!}
                        </div>

                        <!-- Tags -->
                        @if ($post->tags->count() > 0)
                            <div class="mt-4">
                                <h6>Tags:</h6>
                                @foreach ($post->tags as $tag)
                                    <span class="badge bg-primary me-1">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
