<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with('user', 'tags')
            ->when($request->search, function ($q, $search) {
                $q->where('title', 'like', "%{$search}%");
            })
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $tags = Tag::orderBy('sort_order')->get();
        return view('admin.articles.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'status' => 'required|in:draft,published',
        ]);

        $slug = Str::slug($validated['title']);
        if (!$slug) {
            $slug = Str::random(8);
        }
        if (Article::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::random(4);
        }

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('articles', 'public');
        }

        $article = Article::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $slug,
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'cover_image' => $coverPath,
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        if (!empty($validated['tags'])) {
            $article->tags()->attach($validated['tags']);
        }

        return redirect()->route('admin.articles.index')->with('success', '文章创建成功');
    }

    public function edit(Article $article)
    {
        $tags = Tag::orderBy('sort_order')->get();
        $selectedTags = $article->tags->pluck('id')->toArray();
        return view('admin.articles.edit', compact('article', 'tags', 'selectedTags'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'status' => 'required|in:draft,published',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }
            $validated['cover_image'] = $request->file('cover_image')->store('articles', 'public');
        }

        $wasPublished = $article->status === 'published';
        $article->update([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'] ?? null,
            'content' => $validated['content'],
            'cover_image' => $validated['cover_image'] ?? $article->cover_image,
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published' && !$wasPublished ? now() : $article->published_at,
        ]);

        $article->tags()->sync($validated['tags'] ?? []);

        return redirect()->route('admin.articles.index')->with('success', '文章更新成功');
    }

    public function destroy(Article $article)
    {
        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }
        $article->delete();
        return back()->with('success', '文章已删除');
    }
}
