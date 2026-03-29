<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $tags = \Illuminate\Support\Facades\Cache::remember('all_tags', 3600, function () {
            return Tag::orderBy('sort_order')->get();
        });

        $posts = Post::published()
            ->with('user', 'tags', 'approvedComments')
            ->when($request->tag, function ($q, $tagSlug) {
                $q->whereHas('tags', fn($q) => $q->where('slug', $tagSlug));
            })
            ->latest()
            ->paginate(15);

        $currentTag = $request->tag;

        if ($request->ajax()) {
            return response()->json([
                'html' => view('front.partials.post-list', compact('posts'))->render(),
                'next_page_url' => $posts->appends(request()->query())->nextPageUrl(),
                'total' => $posts->total(),
            ]);
        }

        return view('front.index', compact('posts', 'tags', 'currentTag'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'images' => 'nullable|array|max:15',
            'images.*' => 'file|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('posts', 'public');
            }
        }

        $post = $request->user()->posts()->create([
            'content' => $validated['content'],
            'images' => $imagePaths ?: null,
        ]);

        if (!empty($validated['tags'])) {
            $post->tags()->attach($validated['tags']);
        }

        return redirect()->route('moments')->with('success', '发布成功！');
    }

    public function show(Post $post)
    {
        if ($post->status !== 'published') {
            abort(404);
        }

        $post->load('user', 'tags', 'approvedComments');

        return view('front.show', compact('post'));
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($post->images) {
            foreach ($post->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $post->delete();

        return redirect()->route('moments')->with('success', '已删除');
    }
}
