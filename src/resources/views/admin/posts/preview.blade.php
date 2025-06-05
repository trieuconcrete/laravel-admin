@extends('layouts.admin')

@section('title', 'Xem trước: ' . $post->title)

@push('styles')
<style>
    /* Article Preview Styles */
    .article-container {
        max-width: 800px;
        margin: 0 auto;
        font-family: Georgia, serif;
    }
    
    .article-header {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .article-title {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
        color: #111827;
        margin-bottom: 1rem;
    }
    
    .article-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: #6b7280;
        font-size: 0.875rem;
        flex-wrap: wrap;
    }
    
    .article-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .article-excerpt {
        font-size: 1.25rem;
        line-height: 1.6;
        color: #4b5563;
        font-style: italic;
        margin: 1.5rem 0;
    }
    
    .article-featured-image {
        width: 100%;
        height: auto;
        border-radius: 12px;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    .article-content {
        font-size: 1.125rem;
        line-height: 1.75;
        color: #374151;
    }
    
    .article-content h1,
    .article-content h2,
    .article-content h3,
    .article-content h4,
    .article-content h5,
    .article-content h6 {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #111827;
    }
    
    .article-content h1 { font-size: 2rem; }
    .article-content h2 { font-size: 1.75rem; }
    .article-content h3 { font-size: 1.5rem; }
    .article-content h4 { font-size: 1.25rem; }
    .article-content h5 { font-size: 1.125rem; }
    .article-content h6 { font-size: 1rem; }
    
    .article-content p {
        margin-bottom: 1rem;
    }
    
    .article-content ul,
    .article-content ol {
        margin-bottom: 1rem;
        padding-left: 2rem;
    }
    
    .article-content li {
        margin-bottom: 0.5rem;
    }
    
    .article-content blockquote {
        border-left: 4px solid #3b82f6;
        padding-left: 1rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #6b7280;
    }
    
    .article-content pre {
        background-color: #1f2937;
        color: #f3f4f6;
        padding: 1rem;
        border-radius: 8px;
        overflow-x: auto;
        margin: 1.5rem 0;
    }
    
    .article-content code {
        background-color: #f3f4f6;
        color: #dc2626;
        padding: 0.125rem 0.25rem;
        border-radius: 4px;
        font-size: 0.875em;
    }
    
    .article-content pre code {
        background-color: transparent;
        color: inherit;
        padding: 0;
    }
    
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1.5rem 0;
    }
    
    .article-content a {
        color: #3b82f6;
        text-decoration: underline;
    }
    
    .article-content a:hover {
        color: #2563eb;
    }
    
    .article-content table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
    }
    
    .article-content th,
    .article-content td {
        border: 1px solid #e5e7eb;
        padding: 0.75rem;
        text-align: left;
    }
    
    .article-content th {
        background-color: #f9fafb;
        font-weight: 600;
    }
    
    .article-tags {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .tag {
        display: inline-block;
        background-color: #f3f4f6;
        color: #4b5563;
        padding: 0.375rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
    }
    
    .tag:hover {
        background-color: #e5e7eb;
        color: #374151;
    }
    
    /* Status banner */
    .status-banner {
        background-color: #fef3c7;
        border: 1px solid #fcd34d;
        color: #92400e;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-banner.published {
        background-color: #d1fae5;
        border-color: #6ee7b7;
        color: #065f46;
    }
    
    .status-banner.archived {
        background-color: #f3f4f6;
        border-color: #d1d5db;
        color: #374151;
    }
    
    /* Preview controls */
    .preview-controls {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        display: flex;
        gap: 0.75rem;
        z-index: 50;
    }
    
    .preview-control-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 50%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .preview-control-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    /* Device preview */
    .device-preview-toggle {
        position: fixed;
        top: 80px;
        right: 2rem;
        display: flex;
        gap: 0.5rem;
        background-color: white;
        padding: 0.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        z-index: 50;
    }
    
    .device-btn {
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        background-color: #f3f4f6;
        color: #6b7280;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .device-btn.active {
        background-color: #3b82f6;
        color: white;
    }
    
    .device-btn:hover:not(.active) {
        background-color: #e5e7eb;
    }
    
    /* Responsive preview container */
    .preview-wrapper {
        transition: all 0.3s ease;
    }
    
    .preview-wrapper.tablet {
        max-width: 768px;
        margin: 0 auto;
    }
    
    .preview-wrapper.mobile {
        max-width: 375px;
        margin: 0 auto;
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
                <h1 class="text-xl font-semibold text-gray-900">Xem trước bài viết</h1>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.posts.edit', $post) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Chỉnh sửa
                </a>
            </div>
        </div>
    </div>
</header>
@overwrite

@section('content')

<!-- Device Preview Toggle -->
<div class="device-preview-toggle">
    <button class="device-btn active" data-device="desktop">
        <i class="fas fa-desktop"></i>
    </button>
    <button class="device-btn" data-device="tablet">
        <i class="fas fa-tablet-alt"></i>
    </button>
    <button class="device-btn" data-device="mobile">
        <i class="fas fa-mobile-alt"></i>
    </button>
</div>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="preview-wrapper">
        <div class="article-container">
            <!-- Status Banner -->
            @if($post->status === 'draft')
                <div class="status-banner">
                    <i class="fas fa-info-circle"></i>
                    <span>Đây là bản nháp. Bài viết chưa được xuất bản.</span>
                </div>
            @elseif($post->status === 'archived')
                <div class="status-banner archived">
                    <i class="fas fa-archive"></i>
                    <span>Bài viết đã được lưu trữ.</span>
                </div>
            @endif
            
            <!-- Article Header -->
            <header class="article-header">
                <h1 class="article-title">{{ $post->title }}</h1>
                
                <div class="article-meta">
                    <div class="article-meta-item">
                        <img src="{{ $post->author->avatar_url ?? asset('images/default-avatar.png') }}" 
                             alt="{{ $post->author->name }}"
                             class="w-8 h-8 rounded-full">
                        <span>{{ $post->author->name }}</span>
                    </div>
                    
                    @if($post->category)
                    <div class="article-meta-item">
                        <i class="fas fa-folder" style="color: {{ $post->category->color }}"></i>
                        <span>{{ $post->category->name }}</span>
                    </div>
                    @endif
                    
                    <div class="article-meta-item">
                        <i class="fas fa-calendar"></i>
                        <span>{{ $post->published_at ? $post->published_at->format('d/m/Y') : 'Chưa xuất bản' }}</span>
                    </div>
                    
                    <div class="article-meta-item">
                        <i class="fas fa-clock"></i>
                        <span>{{ $post->reading_time }} phút đọc</span>
                    </div>
                    
                    <div class="article-meta-item">
                        <i class="fas fa-eye"></i>
                        <span>{{ number_format($post->views) }} lượt xem</span>
                    </div>
                </div>
                
                @if($post->excerpt)
                <div class="article-excerpt">
                    {{ $post->excerpt }}
                </div>
                @endif
            </header>
            
            <!-- Featured Image -->
            @if($post->featured_image)
            <img src="{{ $post->featured_image_url }}" 
                 alt="{{ $post->title }}"
                 class="article-featured-image">
            @endif
            
            <!-- Article Content -->
            <article class="article-content">
                {!! $post->content !!}
            </article>
            
            <!-- Tags -->
            @if($post->tags->count() > 0)
            <div class="article-tags">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Tags:</h3>
                <div>
                    @foreach($post->tags as $tag)
                        <span class="tag">#{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</main>

<!-- Preview Controls -->
<div class="preview-controls">
    <button class="preview-control-btn" id="toggle-fullscreen" title="Toàn màn hình">
        <i class="fas fa-expand"></i>
    </button>
    
    <a href="{{ route('admin.posts.edit', $post) }}" 
       class="preview-control-btn" 
       title="Chỉnh sửa">
        <i class="fas fa-edit text-blue-600"></i>
    </a>
    
    @if($post->isPublished())
    <a href="{{ url('/blog/' . $post->slug) }}" 
       target="_blank"
       class="preview-control-btn" 
       title="Xem trên website">
        <i class="fas fa-external-link-alt text-green-600"></i>
    </a>
    @endif
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Device preview toggle
    $('.device-btn').click(function() {
        $('.device-btn').removeClass('active');
        $(this).addClass('active');
        
        const device = $(this).data('device');
        const $wrapper = $('.preview-wrapper');
        
        $wrapper.removeClass('tablet mobile');
        if (device === 'tablet') {
            $wrapper.addClass('tablet');
        } else if (device === 'mobile') {
            $wrapper.addClass('mobile');
        }
    });
    
    // Fullscreen toggle
    $('#toggle-fullscreen').click(function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            $(this).find('i').removeClass('fa-expand').addClass('fa-compress');
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
                $(this).find('i').removeClass('fa-compress').addClass('fa-expand');
            }
        }
    });
    
    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // ESC to exit fullscreen
        if (e.keyCode === 27 && document.fullscreenElement) {
            document.exitFullscreen();
            $('#toggle-fullscreen i').removeClass('fa-compress').addClass('fa-expand');
        }
        
        // E to edit
        if (e.keyCode === 69 && !$(e.target).is('input, textarea')) {
            window.location.href = '{{ route("admin.posts.edit", $post) }}';
        }
    });
    
    // Smooth scroll for anchor links
    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        const target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 500);
        }
    });
});
</script>
@endpush