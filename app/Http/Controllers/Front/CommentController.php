<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function storePostComment(Request $request, Post $post)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'content' => 'required|string|max:500',
        ]);

        $post->comments()->create($validated);

        return back()->with('success', '评论已提交，等待审核');
    }

    public function storeArticleComment(Request $request, Article $article)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:50',
            'email' => 'nullable|email|max:100',
            'content' => 'required|string|max:1000',
        ]);

        $article->comments()->create($validated);

        return back()->with('success', '评论已提交，等待审核');
    }
}
