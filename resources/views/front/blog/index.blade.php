@extends('front.layout')

@section('title', $currentTag ? '标签：' . $currentTag : '长文与笔记')
@section('description', '深入的技术探讨、生活感悟以及那些值得长久保留的思考。' . ($currentTag ? '（当前标签：' . $currentTag . '）' : ''))

@section('hero')
    <x-front.hero
        title="长文与笔记"
        subtitle="这里沉淀了深入的技术探讨、生活感悟以及一些值得长久保留的思考。"
        theme="blue" />
@endsection

@section('content')
<div class="max-w-5xl mx-auto">
    @if($tags->count())
    <nav class="flex flex-wrap gap-2.5 mb-10" aria-label="标签筛选">
        <a href="{{ route('home') }}"
           class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium transition-all {{ !$currentTag ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-md shadow-blue-500/20' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-slate-300 hover:bg-slate-50' }}">
            全部
        </a>
        @foreach($tags as $tag)
            <x-front.tag-pill :tag="$tag" :href="route('home', ['tag' => $tag->slug])" :active="$currentTag === $tag->slug" size="lg" />
        @endforeach
    </nav>
    @endif

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
                        @if($article->approved_comments_count > 0)
                            <span aria-hidden="true" class="self-center">&bull;</span>
                            <span class="self-center">{{ $article->approved_comments_count }} 评论</span>
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
                <p class="text-slate-500 text-sm">这里目前还是空空如也</p>
                <p class="text-slate-400 text-xs mt-2">@if($currentTag) 当前标签下没有内容 @else 敬请期待未来更新 @endif</p>
            </div>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $articles->appends(request()->query())->links() }}
    </div>
</div>
@endsection
