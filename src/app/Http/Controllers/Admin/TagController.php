<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Display a listing of tags.
     */
    public function index(Request $request)
    {
        $query = Tag::query();

        // Search by name or slug
        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        }

        $tags = $query->orderBy('created_at', 'desc')->paginate(15);

        if ($request->ajax()) {
            return response()->json($tags);
        }

        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create()
    {
        return view('admin.tags.create');
    }

    /**
     * Store a newly created tag in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags',
            'slug' => 'nullable|string|max:255|unique:tags',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $tag = Tag::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tag' => $tag,
                'message' => 'Tag has been created successfully!'
            ]);
        }

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag has been created successfully!');
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit(Tag $tag)
    {
        return response()->json($tag);
    }

    public function show(Tag $tag)
    {
        return response()->json($tag);
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            'slug' => 'nullable|string|max:255|unique:tags,slug,' . $tag->id,
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $tag->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tag' => $tag,
                'message' => 'Tag has been updated successfully!'
            ]);
        }

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag has been updated successfully!');
    }

    /**
     * Remove the specified tag from storage.
     */
    public function destroy(Tag $tag)
    {
        // Detach related posts before deleting
        $tag->posts()->detach();

        $tag->delete();

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag has been updated successfully!');
    }
}
