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
        if ($article->status !== 'published') {
            abort(404);
        }

        $article->increment('views_count');
        
        // 记录访问详情
        \App\Models\ArticleVisit::create([
            'article_id' => $article->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referer' => $request->headers->get('referer'),
            'user_id' => auth()->id(),
        ]);

        $article->load('user', 'tags', 'approvedComments');

        $relatedArticles = Article::published()
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
