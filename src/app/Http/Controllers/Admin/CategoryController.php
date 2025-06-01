<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('posts');
        
        // Search
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->get('is_active'));
        }
        
        $categories = $query->orderBy('order')->orderBy('name')->paginate(15);
        
        if ($request->ajax()) {
            return response()->json($categories);
        }
        
        return view('admin.categories.index', compact('categories'));
    }
    
    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }
    
    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'nullable|string|max:255|unique:categories',
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);
        
        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $category = Category::create($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Danh mục đã được tạo thành công!'
            ]);
        }
        
        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Danh mục đã được tạo thành công!');
    }
    
    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }
    
    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);
        
        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $category->update($validated);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Danh mục đã được cập nhật!'
            ]);
        }
        
        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Danh mục đã được cập nhật!');
    }
    
    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        // Check if category has posts
        if ($category->posts()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa danh mục đang có bài viết!'
            ], 400);
        }
        
        $category->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Danh mục đã được xóa!'
        ]);
    }
    
    /**
     * Update category order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.order' => 'required|integer|min:0'
        ]);
        
        foreach ($request->categories as $item) {
            Category::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Thứ tự danh mục đã được cập nhật!'
        ]);
    }
    
    /**
     * Toggle category status.
     */
    public function toggleStatus(Category $category)
    {
        $category->update(['is_active' => !$category->is_active]);
        
        return response()->json([
            'success' => true,
            'is_active' => $category->is_active,
            'message' => $category->is_active ? 'Danh mục đã được kích hoạt!' : 'Danh mục đã bị vô hiệu hóa!'
        ]);
    }
}