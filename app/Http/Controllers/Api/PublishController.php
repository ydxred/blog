<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublishController extends Controller
{
    /**
     * Publish an article.
     */
    public function storeArticle(Request $request)
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

        return response()->json([
            'message' => 'Article created successfully',
            'article' => $article
        ], 201);
    }

    /**
     * Publish a short post (dynamic).
     */
    public function storePost(Request $request)
    {
        // Posts might not be as strict as articles but we keep it functional
        $request->validate([
            'content' => 'required|string',
            'status' => 'nullable|in:draft,published',
            'images' => 'nullable|array',
            'images.*' => 'string'
        ]);

        $status = $request->input('status', 'published');

        $post = Post::create([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
            'images' => $request->input('images', []),
            'status' => $status,
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }
}