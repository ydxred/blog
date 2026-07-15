<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Setting;
use App\Models\Tag;

class FeedController extends Controller
{
    /**
     * 站点地图。只收录公开(已发布 + 前台展示)的文章，故用显式 is_visible=true，
     * 不用 scopeVisible（那个对管理员会放行隐藏文章，喂给爬虫不合适）。
     */
    public function sitemap()
    {
        $articles = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('is_visible', true)
            ->latest('published_at')
            ->get(['slug', 'updated_at']);

        $tags = Tag::orderBy('sort_order')->get(['slug']);

        return response()
            ->view('front.sitemap', compact('articles', 'tags'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * RSS 2.0 订阅源（最近 20 篇公开文章）。
     */
    public function rss()
    {
        $articles = Article::where('status', 'published')
            ->whereNotNull('published_at')
            ->where('is_visible', true)
            ->latest('published_at')
            ->take(20)
            ->get();

        $siteTitle = trim((string) Setting::get('site_title', '')) ?: config('app.name');
        $siteTagline = trim((string) Setting::get('site_tagline', '')) ?: config('app.tagline', '记录文字与日常');

        return response()
            ->view('front.rss', compact('articles', 'siteTitle', 'siteTagline'))
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8');
    }
}
