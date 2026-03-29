<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with('commentable')
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('admin.comments.index', compact('comments'));
    }

    public function approve(Comment $comment)
    {
        $comment->update(['status' => 'approved']);
        return back()->with('success', '评论已通过');
    }

    public function reject(Comment $comment)
    {
        $comment->update(['status' => 'rejected']);
        return back()->with('success', '评论已拒绝');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', '评论已删除');
    }
}
