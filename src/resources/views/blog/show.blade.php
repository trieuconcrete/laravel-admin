@extends('layouts.app')

@section('title', $post->title)

@push('meta')
<meta name="description" content="{{ $post->meta_description ?? $post->excerpt }}">
<meta name="keywords" content="{{ $post->tags->pluck('name')->implode(', ') }}">
<meta name="author" content="{{ $post->author->name }}">

<!-- Open Graph -->
<meta property="og:title" content="{{ $post->meta_title ?? $post->title }}">
<meta property="og:description" content="{{ $post->meta_description ?? $post->excerpt }}">
<meta property="og:type" content="article">
<meta property="og:url" content="{{ route('blog.show', $post->slug) }}">
<meta property="og:image" content="{{ $post->featured_image_url }}">
<meta property="article:author" content="{{ $post->author->name }}">
<meta property="article:published_time" content="{{ $post->published_at->toISOString() }}">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $post->meta_title ?? $post->title }}">
<meta name="twitter:description" content="{{ $post->meta_description ?? $post->excerpt }}">
<meta name="twitter:image" content="{{ $post->featured_image_url }}">
@endpush

@push('styles')
<style>
    /* Article content styles */
    .article-content {
        font-size: 1.125rem;
        line-height: 1.8;
        color: #374151;
    }
    
    .article-content h1,
    .article-content h2,
    .article-content h3,
    .article-content h4,
    .article-content h5,
    .article-content h6 {
        font-weight: 700;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #111827;
    }
    
    .article-content h2 { font-size: 1.875rem; }
    .article-content h3 { font-size: 1.5rem; }
    .article-content h4 { font-size: 1.25rem; }
    
    .article-content p {
        margin-bottom: 1.5rem;
    }
    
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 2rem auto;
    }
    
    .article-content blockquote {
        border-left: 4px solid #3b82f6;
        padding-left: 1.5rem;
        margin: 2rem 0;
        font-style: italic;
        color: #6b7280;
    }
    
    .article-content pre {
        background-color: #1f2937;
        color: #f3f4f6;
        padding: 1.5rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 2rem 0;
    }
    
    .article-content code {
        background-color: #f3f4f6;
        padding: 0.125rem 0.375rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
    
    .article-content pre code {
        background-color: transparent;
        padding: 0;
    }
    
    .article-content ul,
    .article-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }
    
    .article-content li {
        margin-bottom: 0.5rem;
    }
    
    /* Share buttons */
    .share-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .share-button:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<!-- Article Header -->
<article>
    <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <!-- Breadcrumb -->
                <nav class="text-sm mb-4 opacity-90">
                    <a href="{{ route('home') }}" class="hover:underline">Trang chủ</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('blog.index') }}" class="hover:underline">Blog</a>
                    @if($post->category)
                    <span class="mx-2">/</span>
                    <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="hover:underline">
                        {{ $post->category->name }}
                    </a>
                    @endif
                </nav>
                
                <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $post->title }}</h1>
                
                <div class="flex items-center space-x-4">
                    <img src="{{ $post->author->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->author->name) }}" 
                         alt="{{ $post->author->name }}"
                         class="w-12 h-12 rounded-full">
                    <div>
                        <p class="font-medium">{{ $post->author->name }}</p>
                        <p class="text-sm opacity-90">
                            <time datetime="{{ $post->published_at->toISOString() }}">
                                {{ $post->published_at->format('d/m/Y') }}
                            </time>
                            • {{ $post->reading_time }} phút đọc
                            • {{ number_format($post->views) }} lượt xem
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Article Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="lg:col-span-2">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-3">
                    @if($post->featured_image)
                    <img src="{{ $post->featured_image_url }}" 
                         alt="{{ $post->title }}" 
                         class="w-full rounded-lg shadow-lg mb-8">
                    @endif
                    
                    <div class="article-content bg-white rounded-lg shadow-lg p-8 mb-8">
                        {!! $post->content !!}
                    </div>
                    
                    <!-- Tags -->
                    @if($post->tags->count() > 0)
                    <div class="mb-8">
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="text-gray-600">Tags:</span>
                            @foreach($post->tags as $tag)
                            <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}"
                               class="inline-block px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                                #{{ $tag->name }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Share Buttons -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                        <h3 class="text-lg font-semibold mb-4">Chia sẻ bài viết</h3>
                        <div class="flex space-x-3">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('blog.show', $post->slug)) }}" 
                               target="_blank"
                               class="share-button bg-blue-600 text-white hover:bg-blue-700">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('blog.show', $post->slug)) }}&text={{ urlencode($post->title) }}" 
                               target="_blank"
                               class="share-button bg-sky-500 text-white hover:bg-sky-600">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('blog.show', $post->slug)) }}" 
                               target="_blank"
                               class="share-button bg-blue-700 text-white hover:bg-blue-800">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            <button onclick="copyToClipboard('{{ route('blog.show', $post->slug) }}')"
                                    class="share-button bg-gray-600 text-white hover:bg-gray-700">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Author Bio -->
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                        <div class="flex items-start space-x-4">
                            <img src="{{ $post->author->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->author->name) }}" 
                                 alt="{{ $post->author->name }}"
                                 class="w-16 h-16 rounded-full">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold mb-2">{{ $post->author->name }}</h3>
                                <p class="text-gray-600 mb-3">{{ $post->author->bio ?? 'Full Stack Developer với đam mê chia sẻ kiến thức và kinh nghiệm về công nghệ.' }}</p>
                                <div class="flex space-x-3">
                                    @if($post->author->github)
                                    <a href="{{ $post->author->github }}" target="_blank" class="text-gray-600 hover:text-gray-900">
                                        <i class="fab fa-github"></i>
                                    </a>
                                    @endif
                                    @if($post->author->linkedin)
                                    <a href="{{ $post->author->linkedin }}" target="_blank" class="text-gray-600 hover:text-blue-600">
                                        <i class="fab fa-linkedin"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        @if($prevPost)
                        <a href="{{ route('blog.show', $prevPost->slug) }}" 
                           class="bg-white rounded-lg shadow-lg p-4 hover:shadow-xl transition-shadow">
                            <p class="text-sm text-gray-500 mb-1">← Bài trước</p>
                            <p class="font-medium line-clamp-2">{{ $prevPost->title }}</p>
                        </a>
                        @endif
                        
                        @if($nextPost)
                        <a href="{{ route('blog.show', $nextPost->slug) }}" 
                           class="bg-white rounded-lg shadow-lg p-4 hover:shadow-xl transition-shadow text-right">
                            <p class="text-sm text-gray-500 mb-1">Bài sau →</p>
                            <p class="font-medium line-clamp-2">{{ $nextPost->title }}</p>
                        </a>
                        @endif
                    </div>
                    
                    <!-- Comments Section -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-semibold mb-6">Bình luận ({{ $post->comments_count }})</h3>
                        
                        <!-- Comment Form -->
                        <form id="comment-form" class="mb-8">
                            @csrf
                            <input type="hidden" name="parent_id" id="parent_id">
                            
                            @guest
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <input type="text" 
                                           name="author_name" 
                                           placeholder="Tên của bạn *"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                           required>
                                </div>
                                <div>
                                    <input type="email" 
                                           name="author_email" 
                                           placeholder="Email của bạn *"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                                           required>
                                </div>
                            </div>
                            @endguest
                            
                            <div class="mb-4">
                                <textarea name="content" 
                                          rows="4"
                                          placeholder="Viết bình luận của bạn..."
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none resize-none"
                                          required></textarea>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-500">
                                    @guest
                                    Email của bạn sẽ không được hiển thị công khai.
                                    @endguest
                                </p>
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Gửi bình luận
                                </button>
                            </div>
                        </form>
                        
                        <!-- Comments List -->
                        <div id="comments-list" class="space-y-6">
                            @include('blog.partials.comments', ['comments' => $comments])
                        </div>
                        
                        @if($comments->hasPages())
                        <div class="mt-6">
                            {{ $comments->links() }}
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Sidebar -->
                <aside class="lg:col-span-1">
                    <!-- Related Posts -->
                    @if($relatedPosts->count() > 0)
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-6">
                        <h3 class="text-lg font-semibold mb-4">Bài viết liên quan</h3>
                        <ul class="space-y-3">
                            @foreach($relatedPosts as $relatedPost)
                            <li>
                                <a href="{{ route('blog.show', $relatedPost->slug) }}" 
                                   class="block hover:text-blue-600 transition-colors">
                                    <h4 class="font-medium line-clamp-2">{{ $relatedPost->title }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">{{ $relatedPost->published_at->format('d/m/Y') }}</p>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</article>
@endsection

@push('scripts')
<script>
// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Link đã được sao chép!');
    });
}

// Handle comment form
$('#comment-form').on('submit', function(e) {
    e.preventDefault();
    
    const form = $(this);
    const button = form.find('button[type="submit"]');
    const originalText = button.text();
    
    button.prop('disabled', true).text('Đang gửi...');
    
    $.ajax({
        url: '{{ route("blog.comment", $post->slug) }}',
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
            if (response.success) {
                form[0].reset();
                alert(response.message);
                
                // Reload comments if approved
                if (response.comment && response.comment.status === 'approved') {
                    location.reload();
                }
            }
        },
        error: function(xhr) {
            alert('Có lỗi xảy ra. Vui lòng thử lại!');
        },
        complete: function() {
            button.prop('disabled', false).text(originalText);
        }
    });
});

// Handle reply
function replyToComment(commentId, authorName) {
    $('#parent_id').val(commentId);
    $('#comment-form textarea').attr('placeholder', 'Trả lời ' + authorName + '...').focus();
    
    // Scroll to form
    $('html, body').animate({
        scrollTop: $('#comment-form').offset().top - 100
    }, 500);
}

// Handle like/dislike
function voteComment(commentId, type) {
    $.post('/comments/' + commentId + '/vote', {
        _token: '{{ csrf_token() }}',
        type: type
    }, function(response) {
        if (response.success) {
            $('#like-count-' + commentId).text(response.likes);
            $('#dislike-count-' + commentId).text(response.dislikes);
        } else {
            alert(response.message);
        }
    });
}
</script>
@endpush