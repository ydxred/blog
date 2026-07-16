@extends('front.layout')

@section('title', $article->title)
@section('description', $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 160))
@section('og_type', 'article')
@if($article->cover_image)
    @section('og_image', asset('storage/' . $article->cover_image))
@endif
@section('canonical', route('article.show', $article->slug))
@section('main_max', '84rem')

@push('head')
{{-- JSON-LD（Article）方便搜索引擎收录 --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BlogPosting',
    'headline' => $article->title,
    'description' => $article->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($article->content), 160),
    'image' => $article->cover_image ? asset('storage/' . $article->cover_image) : null,
    'datePublished' => $article->published_at->toIso8601String(),
    'dateModified' => $article->updated_at->toIso8601String(),
    'author' => ['@type' => 'Person', 'name' => $article->user->name],
    'mainEntityOfPage' => route('article.show', $article->slug),
    'publisher' => [
        '@type' => 'Organization',
        'name' => config('app.name'),
        'url' => url('/'),
    ],
    'keywords' => $article->tags->pluck('name')->implode(', '),
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}
</script>
@endpush

@push('scripts')
    @vite(['resources/js/article.js', 'resources/js/share.js'])
    <script src="{{ asset('lib/social-share.min.js') }}" defer></script>
@endpush

@section('content')
<div class="flex flex-col lg:flex-row gap-8 items-start">
    <div class="flex-1 min-w-0 w-full">
        <div class="mb-6">
            <x-front.back-link :href="route('home')" label="返回列表" theme="indigo" />
        </div>

        <article class="rounded-2xl border border-slate-200/80 bg-white/90 backdrop-blur-sm p-6 sm:p-10 shadow-sm mb-10 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-400 to-indigo-500" aria-hidden="true"></div>
            <header class="mb-8 mt-2">
                @if($article->tags->count())
                    <div class="flex flex-wrap gap-2 mb-5">
                        @foreach($article->tags as $tag)
                            <x-front.tag-pill :tag="$tag" :href="route('home', ['tag' => $tag->slug])" />
                        @endforeach
                    </div>
                @endif

                <h1 class="text-3xl sm:text-4xl font-title font-bold text-slate-800 leading-snug tracking-tight mb-6">{{ $article->title }}</h1>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-500">
                    <span class="flex items-center gap-2">
                        @if($article->user->avatar)
                            <img src="{{ asset('storage/' . $article->user->avatar) }}" alt="{{ $article->user->name }} 头像" class="w-8 h-8 rounded-full object-cover ring-2 ring-indigo-50" loading="lazy" decoding="async">
                        @endif
                        <span class="font-medium text-slate-700">{{ $article->user->name }}</span>
                    </span>
                    <span aria-hidden="true">&bull;</span>
                    <time datetime="{{ $article->published_at->toIso8601String() }}">{{ $article->published_at->format('Y年n月j日') }}</time>
                    <span aria-hidden="true">&bull;</span>
                    <span>{{ $article->read_time }} 分钟阅读</span>
                    @if($article->views_count > 0)
                        <span aria-hidden="true">&bull;</span>
                        <span>{{ $article->views_count }} 次阅读</span>
                    @endif
                </div>
            </header>

            @if($article->cover_image)
                <div class="mb-10 rounded-xl overflow-hidden ring-1 ring-slate-200/60 shadow-sm">
                    <img src="{{ asset('storage/' . $article->cover_image) }}" alt="{{ $article->title }} 封面" class="w-full object-cover max-h-[32rem]" loading="lazy" decoding="async">
                </div>
            @endif

            <div class="prose prose-slate prose-lg max-w-none
                        prose-headings:font-title prose-headings:font-bold prose-headings:text-slate-800
                        prose-a:text-indigo-600 hover:prose-a:text-indigo-500
                        prose-code:text-violet-600 prose-code:bg-slate-100/80 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded-md prose-code:font-medium prose-code:before:hidden prose-code:after:hidden
                        prose-pre:bg-slate-900 prose-pre:text-slate-50 prose-pre:shadow-sm prose-pre:p-0 prose-pre:my-4 prose-pre:rounded-xl prose-pre:overflow-hidden
                        prose-img:rounded-xl prose-img:ring-1 prose-img:ring-slate-200/60
                        prose-blockquote:border-l-indigo-400 prose-blockquote:bg-indigo-50/50 prose-blockquote:py-1 prose-blockquote:pr-4 prose-blockquote:rounded-r-lg prose-blockquote:text-slate-600 prose-blockquote:not-italic
                        prose-table:text-sm prose-th:bg-slate-50 prose-th:font-semibold">
                {!! \App\Services\MarkdownRenderer::toHtml($article->content) !!}
            </div>

            <div class="mt-12 pt-6 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <x-front.like-button type="article" :id="$article->id" :count="$article->likes_count" />
                    <span class="text-sm font-medium text-slate-500">分享这篇文章</span>
                </div>
                <div class="social-share" data-sites="weibo,qq,wechat,twitter" data-mobile-sites="weibo,qq,wechat,twitter"
                     data-url="{{ route('article.show', $article->slug) }}" data-title="{{ $article->title }}"
                     data-description="{{ $article->excerpt ?: Str::limit(strip_tags($article->content), 120) }}">
                </div>
            </div>
        </article>

        @if($relatedArticles->count())
        <section class="mb-10" aria-label="相关阅读">
            <h2 class="text-sm font-bold text-stone-400 uppercase tracking-widest mb-5 ml-1">Related / 相关阅读</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach($relatedArticles as $related)
                    <a href="{{ route('article.show', $related->slug) }}" class="group flex gap-4 rounded-xl border border-stone-200/80 bg-white/60 p-3 hover:bg-white hover:border-indigo-200 hover:shadow-md hover:shadow-indigo-500/5 transition-all">
                        @if($related->cover_image)
                            <div class="w-24 h-20 shrink-0 rounded-lg overflow-hidden bg-stone-100 ring-1 ring-stone-200/50">
                                <img src="{{ asset('storage/' . $related->cover_image) }}" alt="{{ $related->title }} 缩略图" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" decoding="async">
                            </div>
                        @endif
                        <div class="min-w-0 flex flex-col justify-center">
                            <p class="text-sm font-bold text-stone-800 line-clamp-2 group-hover:text-indigo-600 transition-colors leading-snug">{{ $related->title }}</p>
                            <p class="text-xs text-stone-400 mt-2 font-medium">{{ $related->published_at->format('Y-m-d') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
        @endif

        <x-front.comment-section
            :comments="$article->approvedComments"
            :action="route('article.comment', $article)"
            theme="indigo"
            heading="评论"
            reply-label="发表评论"
            submit-label="提交评论" />
    </div>

    <aside class="article-toc hidden lg:block w-72 shrink-0 sticky top-20" aria-label="文章目录">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 shadow-sm ring-1 ring-slate-200/80">
            <h3 class="font-bold text-slate-800 mb-3 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                文章目录
            </h3>
            <div id="toc" class="text-sm"></div>
        </div>
    </aside>
</div>
@endsection
