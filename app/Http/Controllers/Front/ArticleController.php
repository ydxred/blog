<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $tags = \Illuminate\Support\Facades\Cache::remember('all_tags', 3600, function () {
            return Tag::orderBy('sort_order')->get();
        });

        $articles = Article::published()
            ->visible()
            ->with('user', 'tags')
            ->withCount('approvedComments')
            ->when($request->tag, function ($q, $tagSlug) {
                $q->whereHas('tags', fn($q) => $q->where('slug', $tagSlug));
            })
            ->latest('published_at')
            ->paginate(10);

        $currentTag = $request->tag;

        return view('front.blog.index', compact('articles', 'tags', 'currentTag'));
    }

    public function show(Request $request, Article $article)
    {
        $isAdmin = auth()->check() && auth()->user()->isAdmin();
        if ($article->status !== 'published' || (!$article->is_visible && !$isAdmin)) {
            abort(404);
        }

        // 浏览统计异步化：响应已发回客户端后再写库，不阻塞 TTFB
        $articleId = $article->id;
        $ip        = $request->ip();
        $ua        = $request->userAgent();
        $referer   = $request->headers->get('referer');
        $userId    = auth()->id();
        dispatch(function () use ($articleId, $ip, $ua, $referer, $userId) {
            try {
                \App\Models\Article::where('id', $articleId)->increment('views_count');
                \App\Models\ArticleVisit::create([
                    'article_id' => $articleId,
                    'ip_address' => $ip,
                    'user_agent' => $ua,
                    'referer'    => $referer,
                    'user_id'    => $userId,
                ]);
            } catch (\Throwable $e) {
                \Log::warning('article view tracking failed: '.$e->getMessage());
            }
        })->afterResponse();

        $article->load('user', 'tags', 'approvedComments');

        $relatedArticles = Article::published()
            ->visible()
            ->where('id', '!=', $article->id)
            ->whereHas('tags', function ($q) use ($article) {
                $q->whereIn('tags.id', $article->tags->pluck('id'));
            })
            ->with('user')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('front.blog.show', compact('article', 'relatedArticles'));
    }
}
