@extends('front.layout')

@section('title', $q !== '' ? '搜索：' . $q : '搜索')
@section('description', '在博客里搜索文章标题与内容。')

@section('hero')
    <x-front.hero
        title="搜索"
        subtitle="在长文与笔记里找点什么。"
        theme="blue" />
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    <form action="{{ route('search') }}" method="GET" class="mb-8">
        <div class="relative max-w-2xl">
            <input type="search" name="q" value="{{ $q }}" placeholder="搜索文章…" autofocus maxlength="100"
                   class="w-full rounded-full border-slate-200 bg-white/80 backdrop-blur-sm py-3 pl-12 pr-4 text-base shadow-sm ring-1 ring-slate-200/80 focus:border-blue-400 focus:ring-2 focus:ring-blue-200">
            <svg class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
        </div>
    </form>

    @if($q === '')
        <p class="text-slate-500 text-sm">输入关键词，搜索文章标题、摘要与正文。</p>
    @else
        <p class="text-sm text-slate-500 mb-6">
            “<span class="font-medium text-slate-700">{{ $q }}</span>” 找到 {{ $articles->total() }} 篇结果
        </p>

        <div class="space-y-6">
            @forelse($articles as $article)
                <article class="group relative rounded-2xl bg-white/80 backdrop-blur-sm p-6 sm:p-8 shadow-sm ring-1 ring-slate-200/80 transition-all hover:shadow-lg hover:shadow-blue-500/10 hover:-translate-y-0.5 overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-blue-400 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity" aria-hidden="true"></div>
                    <a href="{{ route('article.show', $article->slug) }}" class="block">
                        <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-500 mb-3 font-medium">
                            <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $article->user->name }}</span>
                            <span aria-hidden="true" class="self-center">&bull;</span>
                            <time datetime="{{ $article->published_at->toIso8601String() }}" class="self-center">{{ $article->published_at->format('Y年n月j日') }}</time>
                            <span aria-hidden="true" class="self-center">&bull;</span>
                            <span class="self-center">{{ $article->read_time }} 分钟阅读</span>
                            @if($article->views_count > 0)
                                <span aria-hidden="true" class="self-center">&bull;</span>
                                <span class="self-center">{{ $article->views_count }} 次阅读</span>
                            @endif
                        </div>
                        <h2 class="text-xl sm:text-2xl font-title font-bold text-slate-800 group-hover:text-blue-600 transition-colors mb-3 leading-snug">{{ $article->title }}</h2>
                        <p class="text-slate-600 leading-relaxed line-clamp-2">
                            {{ $article->excerpt ?: Str::limit(strip_tags($article->content), 180) }}
                        </p>
                        @if($article->tags->count())
                            <div class="flex flex-wrap gap-2 mt-4">
                                @foreach($article->tags as $tag)
                                    <span class="text-xs px-2.5 py-1 rounded-md font-medium"
                                          style="color: {{ $tag->color }}; background-color: {{ $tag->color }}14;">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </a>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white/50 backdrop-blur-sm py-20 text-center">
                    <p class="text-slate-500 text-sm">没有找到相关文章</p>
                    <p class="text-slate-400 text-xs mt-2">换个关键词试试</p>
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $articles->links() }}
        </div>
    @endif
</div>
@endsection
