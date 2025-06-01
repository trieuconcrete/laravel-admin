<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Display a listing of posts.
     */
    public function index(Request $request)
    {
        $query = Post::with(['author', 'category']);
        
        // Search
        if ($search = $request->get('search')) {
            $query->search($search);
        }
        
        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
        
        // Filter by category
        if ($categoryId = $request->get('category_id')) {
            $query->inCategory($categoryId);
        }
        
        // Filter by author
        if ($authorId = $request->get('author_id')) {
            $query->where('author_id', $authorId);
        }
        
        // Date filter
        if ($dateFilter = $request->get('date_filter')) {
            switch($dateFilter) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
            }
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Get statistics
        $stats = [
            'total' => Post::count(),
            'published' => Post::published()->count(),
            'draft' => Post::draft()->count(),
            'views' => Post::sum('views')
        ];
        
        // Paginate
        $posts = $query->paginate(10)->withQueryString();
        
        // Get categories for filter
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        if ($request->ajax()) {
            return response()->json([
                'posts' => $posts,
                'stats' => $stats
            ]);
        }
        
        return view('admin.posts.index', compact('posts', 'stats', 'categories'));
    }
    
    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        
        return view('admin.posts.create', compact('categories', 'tags'));
    }
    
    /**
     * Store a newly created post.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'published_at' => 'nullable|date'
        ]);
        
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('posts', 'public');
            $validated['featured_image'] = $path;
        }
        
        // Create post
        $post = Post::create($validated);
        
        // Sync tags
        if (!empty($validated['tags'])) {
            $post->syncTags($validated['tags']);
        }
        
        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Bài viết đã được tạo thành công!');
    }
    
    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        $post->load(['author', 'category', 'tags']);
        
        if (request()->ajax()) {
            return response()->json($post);
        }
        
        return view('admin.posts.show', compact('post'));
    }
    
    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $post->load('tags');
        
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }
    
    /**
     * Update the specified post.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,' . $post->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:draft,published,archived',
            'featured_image' => 'nullable|image|max:2048',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'published_at' => 'nullable|date'
        ]);
        
        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            
            $path = $request->file('featured_image')->store('posts', 'public');
            $validated['featured_image'] = $path;
        }
        
        // Update post
        $post->update($validated);
        
        // Sync tags
        if (isset($validated['tags'])) {
            $post->syncTags($validated['tags']);
        } else {
            $post->tags()->detach();
        }
        
        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Bài viết đã được cập nhật!');
    }
    
    /**
     * Update post status.
     */
    public function updateStatus(Request $request, Post $post)
    {
        $request->validate([
            'status' => 'required|in:draft,published,archived'
        ]);
        
        $post->update(['status' => $request->status]);
        
        if ($request->status === 'published' && !$post->published_at) {
            $post->update(['published_at' => now()]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đã được cập nhật!'
        ]);
    }
    
    /**
     * Remove the specified post.
     */
    public function destroy(Post $post)
    {
        // Delete featured image
        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }
        
        // Delete post
        $post->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Bài viết đã được xóa!'
        ]);
    }
    
    /**
     * Bulk update posts.
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:posts,id',
            'action' => 'required|in:publish,draft,archive,delete'
        ]);
        
        $posts = Post::whereIn('id', $request->ids);
        
        switch($request->action) {
            case 'publish':
                $posts->update([
                    'status' => 'published',
                    'published_at' => now()
                ]);
                $message = 'Đã xuất bản các bài viết';
                break;
                
            case 'draft':
                $posts->update(['status' => 'draft']);
                $message = 'Đã chuyển về nháp';
                break;
                
            case 'archive':
                $posts->update(['status' => 'archived']);
                $message = 'Đã lưu trữ các bài viết';
                break;
                
            case 'delete':
                // Delete featured images
                $posts->get()->each(function($post) {
                    if ($post->featured_image) {
                        Storage::disk('public')->delete($post->featured_image);
                    }
                });
                $posts->delete();
                $message = 'Đã xóa các bài viết';
                break;
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    /**
     * Upload image for content editor.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);
        
        $path = $request->file('image')->store('posts/content', 'public');
        
        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $path)
        ]);
    }
    
    /**
     * Preview post.
     */
    public function preview(Post $post)
    {
        $post->load(['author', 'category', 'tags']);
        
        return view('admin.posts.preview', compact('post'));
    }
}