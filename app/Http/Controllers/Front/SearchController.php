<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $q = mb_substr($q, 0, 100); // 限长，避免超长关键词

        $articles = null;
        if ($q !== '') {
            // 转义 LIKE 通配符，避免用户输入的 % / _ 被当通配符
            $term = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $q);
            $like = '%' . $term . '%';

            $articles = Article::published()->visible()
                ->with('user', 'tags')
                ->withCount('approvedComments')
                ->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('excerpt', 'like', $like)
                        ->orWhere('content', 'like', $like);
                })
                ->latest('published_at')
                ->paginate(10)
                ->appends(['q' => $q]);
        }

        $tags = Cache::remember('all_tags', 3600, fn () => Tag::orderBy('sort_order')->get());

        return view('front.search', compact('articles', 'q', 'tags'));
    }
}
