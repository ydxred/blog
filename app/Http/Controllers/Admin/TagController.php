<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags',
            'color' => 'required|string|max:7',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        if (Tag::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] .= '-' . Str::random(4);
        }

        Tag::create($validated);

        return redirect()->route('admin.tags.index')->with('success', '标签创建成功');
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tags,name,' . $tag->id,
            'color' => 'required|string|max:7',
            'sort_order' => 'integer|min:0',
        ]);

        $tag->update($validated);

        return redirect()->route('admin.tags.index')->with('success', '标签更新成功');
    }

    public function destroy(Tag $tag)
    {
        $tag->posts()->detach();
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', '标签已删除');
    }
}
