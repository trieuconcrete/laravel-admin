<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prompt;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromptController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $prompts = Prompt::with(['user', 'category', 'tags'])
            ->latest()
            ->paginate(15);

        return view('admin.prompts.index', compact('prompts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        
        return view('admin.prompts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:prompts,slug',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'example_output' => 'nullable|string',
            'variable_descriptions' => 'nullable|json',
            'is_public' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id'
        ]);

        // Xử lý slug
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Xử lý variable_descriptions
        if ($request->has('variable_descriptions')) {
            $validated['variable_descriptions'] = json_decode($request->variable_descriptions, true);
        }

        // Thêm user_id
        $validated['user_id'] = auth()->id();
        
        // Tạo prompt
        $prompt = Prompt::create($validated);

        // Gắn tags
        if ($request->has('tags')) {
            $prompt->tags()->sync($request->tags);
        }

        return redirect()
            ->route('admin.prompts.index')
            ->with('success', 'Prompt đã được tạo thành công!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Prompt $prompt)
    {
        $prompt->load(['user', 'category', 'tags']);
        
        return view('admin.prompts.show', compact('prompt'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Prompt $prompt)
    {
        $categories = Category::all();
        $tags = Tag::all();
        $selectedTags = $prompt->tags->pluck('id')->toArray();

        return view('admin.prompts.edit', compact('prompt', 'categories', 'tags', 'selectedTags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prompt $prompt)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:prompts,slug,' . $prompt->id,
            'description' => 'nullable|string',
            'content' => 'required|string',
            'example_output' => 'nullable|string',
            'variable_descriptions' => 'nullable|json',
            'is_public' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id'
        ]);

        // Xử lý slug
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Xử lý variable_descriptions
        if ($request->has('variable_descriptions')) {
            $validated['variable_descriptions'] = json_decode($request->variable_descriptions, true);
        }

        // Cập nhật prompt
        $prompt->update($validated);

        // Sync tags
        if ($request->has('tags')) {
            $prompt->tags()->sync($request->tags);
        } else {
            $prompt->tags()->detach();
        }

        return redirect()
            ->route('admin.prompts.index')
            ->with('success', 'Prompt đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prompt $prompt)
    {
        $prompt->delete();

        return redirect()
            ->route('admin.prompts.index')
            ->with('success', 'Prompt đã được xóa thành công!');
    }

    /**
     * Toggle public status
     */
    public function togglePublic(Prompt $prompt)
    {
        $prompt->is_public = !$prompt->is_public;
        $prompt->save();

        return redirect()
            ->route('admin.prompts.index')
            ->with('success', 'Trạng thái công khai đã được cập nhật!');
    }
}
