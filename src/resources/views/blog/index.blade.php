@extends('layouts.app')

@section('title', 'Blog')

@push('meta')
<meta name="description" content="Đọc các bài viết mới nhất về lập trình, công nghệ và kinh nghiệm từ Nguyen Trieu">
<meta property="og:title" content="Blog - Nguyen Trieu">
<meta property="og:description" content="Chia sẻ kiến thức và kinh nghiệm về lập trình">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
@endpush

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Blog</h1>
            <p class="text-xl opacity-90">Chia sẻ kiến thức, kinh nghiệm và những điều thú vị về công nghệ</p>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Posts Column -->
            <div class="lg:col-span-2">
                @if(request('category') || request('tag') || request('q'))
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-blue-700">
                        @if(request('category'))
                            Danh mục: <span class="font-semibold">{{ $categories->firstWhere('slug', request('category'))->name ?? '' }}</span>
                        @elseif(request('tag'))
                            Tag: <span class="font-semibold">{{ request('tag') }}</span>
                        @elseif(request('q'))
                            Kết quả tìm kiếm cho: <span class="font-semibold">"{{ request('q') }}"</span>
                        @endif
                        <a href="{{ route('blog.index') }}" class="ml-2 text-blue-600 hover:underline">Xóa bộ lọc</a>
                    </p>
                </div>
                @endif
                
                <div class="space-y-8">
                    @forelse($posts as $post)
                    <article class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                        @if($post->featured_image)
                        <a href="{{ route('blog.show', $post->slug) }}">
                            <img src="{{ $post->featured_image_url }}" 
                                 alt="{{ $post->title }}" 
                                 class="w-full h-64 object-cover hover:opacity-90 transition-opacity">
                        </a>
                        @endif
                        
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <img src="{{ $post->author->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->author->name) }}" 
                                     alt="{{ $post->author->name }}"
                                     class="w-8 h-8 rounded-full mr-2">
                                <span>{{ $post->author->name }}</span>
                                <span class="mx-2">•</span>
                                <time datetime="{{ $post->published_at->toISOString() }}">
                                    {{ $post->published_at->format('d/m/Y') }}
                                </time>
                                <span class="mx-2">•</span>
                                <span>{{ $post->reading_time }} phút đọc</span>
                            </div>
                            
                            <h2 class="text-2xl font-bold mb-3">
                                <a href="{{ route('blog.show', $post->slug) }}" 
                                   class="text-gray-900 hover:text-blue-600 transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h2>
                            
                            <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    @if($post->category)
                                    <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}"
                                       class="inline-block px-3 py-1 text-xs rounded-full hover:opacity-80 transition-opacity"
                                       style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }}">
                                        {{ $post->category->name }}
                                    </a>
                                    @endif
                                    
                                    @foreach($post->tags->take(3) as $tag)
                                    <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}"
                                       class="text-sm text-gray-500 hover:text-blue-600">
                                        #{{ $tag->name }}
                                    </a>
                                    @endforeach
                                </div>
                                
                                <a href="{{ route('blog.show', $post->slug) }}" 
                                   class="text-blue-600 hover:text-blue-700 font-medium flex items-center">
                                    Đọc tiếp
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </article>
                    @empty
                    <div class="text-center py-12">
                        <svg class="w-24 h-24 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-gray-500">Không tìm thấy bài viết nào.</p>
                    </div>
                    @endforelse
                </div>
                
                <!-- Pagination -->
                @if($posts->hasPages())
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <aside class="space-y-8">
                <!-- Search -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Tìm kiếm</h3>
                    <form action="{{ route('blog.search') }}" method="GET">
                        <div class="relative">
                            <input type="text" 
                                   name="q" 
                                   value="{{ request('q') }}"
                                   placeholder="Tìm kiếm bài viết..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                            <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </form>
                </div>
                
                <!-- Categories -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Danh mục</h3>
                    <ul class="space-y-2">
                        @foreach($categories as $category)
                        <li>
                            <a href="{{ route('blog.index', ['category' => $category->slug]) }}" 
                               class="flex items-center justify-between p-2 rounded hover:bg-gray-50 transition-colors {{ request('category') == $category->slug ? 'bg-blue-50 text-blue-600' : '' }}">
                                <span class="flex items-center">
                                    <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $category->color }}"></span>
                                    {{ $category->name }}
                                </span>
                                <span class="text-sm text-gray-500">({{ $category->posts_count }})</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                <!-- Popular Posts -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Bài viết phổ biến</h3>
                    <ul class="space-y-3">
                        @foreach($popularPosts as $popularPost)
                        <li>
                            <a href="{{ route('blog.show', $popularPost->slug) }}" 
                               class="block hover:text-blue-600 transition-colors">
                                <h4 class="font-medium line-clamp-2">{{ $popularPost->title }}</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ number_format($popularPost->views) }} lượt xem
                                </p>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                
                <!-- Tags -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                        <a href="{{ route('blog.index', ['tag' => $tag->slug]) }}" 
                           class="inline-block px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                            {{ $tag->name }} ({{ $tag->posts_count }})
                        </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Archives -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Lưu trữ</h3>
                    <ul class="space-y-2">
                        @foreach($archives as $archive)
                        <li>
                            <a href="{{ route('blog.index', ['month' => $archive->month]) }}" 
                               class="flex items-center justify-between p-2 rounded hover:bg-gray-50 transition-colors">
                                <span>{{ \Carbon\Carbon::parse($archive->month . '-01')->format('F Y') }}</span>
                                <span class="text-sm text-gray-500">({{ $archive->count }})</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
// Add any blog-specific JavaScript here
</script>
@endpush