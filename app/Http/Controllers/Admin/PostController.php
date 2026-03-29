<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with('user', 'tags', 'comments')
            ->when($request->search, function ($q, $search) {
                $q->where('content', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    public function toggleStatus(Post $post)
    {
        $post->status = $post->status === 'published' ? 'hidden' : 'published';
        $post->save();

        return back()->with('success', '状态已更新');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('success', '帖子已删除');
    }
}
