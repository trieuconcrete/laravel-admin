<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class BlogController extends Controller
{
    /**
     * Display blog listing.
     */
    public function index(Request $request)
    {
        $query = Post::published()
                     ->with(['author', 'category', 'tags'])
                     ->withCount('comments');
        
        // Search
        if ($search = $request->get('q')) {
            $query->search($search);
        }
        
        // Category filter
        if ($category = $request->get('category')) {
            $categoryModel = Category::where('slug', $category)->firstOrFail();
            $query->where('category_id', $categoryModel->id);
        }
        
        // Tag filter
        if ($tag = $request->get('tag')) {
            $tagModel = Tag::where('slug', $tag)->firstOrFail();
            $query->whereHas('tags', function($q) use ($tagModel) {
                $q->where('tags.id', $tagModel->id);
            });
        }
        
        // Archive filter
        if ($month = $request->get('month')) {
            $query->whereMonth('published_at', date('m', strtotime($month)))
                  ->whereYear('published_at', date('Y', strtotime($month)));
        }
        
        $posts = $query->latest('published_at')->paginate(10);
        
        // Get sidebar data
        $categories = Category::where('is_active', true)
                             ->withCount('posts')
                             ->orderBy('order')
                             ->get();
        
        $popularPosts = Post::published()
                           ->orderBy('views', 'desc')
                           ->limit(5)
                           ->get();
        
        $recentPosts = Post::published()
                          ->latest('published_at')
                          ->limit(5)
                          ->get();
        
        $tags = Tag::withCount('posts')
                   ->having('posts_count', '>', 0)
                   ->orderBy('posts_count', 'desc')
                   ->limit(20)
                   ->get();
        
        $archives = Post::published()
                       ->selectRaw('DATE_FORMAT(published_at, "%Y-%m") as month, COUNT(*) as count')
                       ->groupBy('month')
                       ->orderBy('month', 'desc')
                       ->limit(12)
                       ->get();
        
        return view('blog.index', compact(
            'posts', 
            'categories', 
            'popularPosts', 
            'recentPosts', 
            'tags', 
            'archives'
        ));
    }
    
    /**
     * Display single post.
     */
    public function show($slug)
    {
        $post = Post::published()
                    ->where('slug', $slug)
                    ->with(['author', 'category', 'tags'])
                    ->firstOrFail();
        
        // Increment views (with cookie check)
        $cookieName = 'post_viewed_' . $post->id;
        if (!Cookie::get($cookieName)) {
            $post->incrementViews();
            Cookie::queue($cookieName, true, 60); // 60 minutes
        }
        
        // Get related posts
        $relatedPosts = Post::published()
                           ->where('id', '!=', $post->id)
                           ->where(function($query) use ($post) {
                               $query->where('category_id', $post->category_id)
                                     ->orWhereHas('tags', function($q) use ($post) {
                                         $q->whereIn('tags.id', $post->tags->pluck('id'));
                                     });
                           })
                           ->limit(4)
                           ->inRandomOrder()
                           ->get();
        
        // Get approved comments with replies
        $comments = Comment::where('post_id', $post->id)
                          ->approved()
                          ->root()
                          ->with(['user', 'approvedReplies.user'])
                          ->latest()
                          ->paginate(20);
        
        // Get next and previous posts
        $nextPost = Post::published()
                       ->where('published_at', '>', $post->published_at)
                       ->orderBy('published_at', 'asc')
                       ->first();
        
        $prevPost = Post::published()
                       ->where('published_at', '<', $post->published_at)
                       ->orderBy('published_at', 'desc')
                       ->first();
        
        return view('blog.show', compact(
            'post', 
            'relatedPosts', 
            'comments', 
            'nextPost', 
            'prevPost'
        ));
    }
    
    /**
     * Store comment.
     */
    public function storeComment(Request $request, Post $post)
    {
        $rules = [
            'content' => 'required|string|min:3|max:1000',
            'parent_id' => 'nullable|exists:comments,id'
        ];
        
        // If user is not authenticated, require name and email
        if (!auth()->check()) {
            $rules['author_name'] = 'required|string|max:100';
            $rules['author_email'] = 'required|email|max:100';
        }
        
        $validated = $request->validate($rules);
        
        $comment = new Comment();
        $comment->post_id = $post->id;
        $comment->content = $validated['content'];
        $comment->parent_id = $validated['parent_id'] ?? null;
        
        if (auth()->check()) {
            $comment->user_id = auth()->id();
            $comment->status = Comment::STATUS_APPROVED;
            $comment->approved_at = now();
        } else {
            $comment->author_name = $validated['author_name'];
            $comment->author_email = $validated['author_email'];
            $comment->status = Comment::STATUS_PENDING;
        }
        
        $comment->save();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $comment->isApproved() 
                    ? 'Bình luận đã được đăng!' 
                    : 'Bình luận của bạn đang chờ duyệt.',
                'comment' => $comment->load('user')
            ]);
        }
        
        return redirect()->back()->with(
            'success', 
            $comment->isApproved() 
                ? 'Bình luận đã được đăng!' 
                : 'Bình luận của bạn đang chờ duyệt.'
        );
    }
    
    /**
     * Like/Unlike comment.
     */
    public function likeComment(Request $request, Comment $comment)
    {
        $type = $request->get('type', 'like');
        $cookieName = 'comment_' . $type . '_' . $comment->id;
        
        if (Cookie::get($cookieName)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã ' . ($type === 'like' ? 'thích' : 'không thích') . ' bình luận này!'
            ]);
        }
        
        if ($type === 'like') {
            $comment->increment('likes');
        } else {
            $comment->increment('dislikes');
        }
        
        Cookie::queue($cookieName, true, 60 * 24 * 30); // 30 days
        
        return response()->json([
            'success' => true,
            'likes' => $comment->likes,
            'dislikes' => $comment->dislikes
        ]);
    }
    
    /**
     * Search posts.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('blog.index');
        }
        
        $posts = Post::published()
                     ->search($query)
                     ->with(['author', 'category', 'tags'])
                     ->paginate(10)
                     ->appends(['q' => $query]);
        
        return view('blog.search', compact('posts', 'query'));
    }
}