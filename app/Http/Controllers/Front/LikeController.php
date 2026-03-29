<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Article;
use App\Models\Post;

class LikeController extends Controller
{
    public function toggle(Request $request, $type, $id)
    {
        $ip = $request->ip();

        if ($type === 'article') {
            $model = Article::published()->findOrFail($id);
            $modelClass = Article::class;
        } elseif ($type === 'post') {
            $model = Post::findOrFail($id);
            $modelClass = Post::class;
        } else {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $existingLike = DB::table('likes')
            ->where('likeable_type', $modelClass)
            ->where('likeable_id', $id)
            ->where('ip_address', $ip)
            ->first();

        if ($existingLike) {
            DB::table('likes')->where('id', $existingLike->id)->delete();
            $model->decrement('likes_count');
            $liked = false;
        } else {
            DB::table('likes')->insert([
                'likeable_type' => $modelClass,
                'likeable_id' => $id,
                'ip_address' => $ip,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $model->increment('likes_count');
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $model->fresh()->likes_count
        ]);
    }

    public function status(Request $request)
    {
        $ip = $request->ip();
        $likes = DB::table('likes')->where('ip_address', $ip)->get(['likeable_type', 'likeable_id']);
        
        $likedArticles = $likes->where('likeable_type', Article::class)->pluck('likeable_id');
        $likedPosts = $likes->where('likeable_type', Post::class)->pluck('likeable_id');

        return response()->json([
            'articles' => $likedArticles,
            'posts' => $likedPosts
        ]);
    }
}
