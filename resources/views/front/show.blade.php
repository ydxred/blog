@extends('front.layout')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('moments') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 hover:text-teal-600 transition-colors bg-white/50 backdrop-blur-sm px-3 py-1.5 rounded-full ring-1 ring-stone-200/80 hover:ring-teal-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            返回动态
        </a>
    </div>

    <article class="rounded-2xl border border-slate-200/80 bg-white/90 backdrop-blur-sm p-6 sm:p-8 shadow-sm mb-8 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-emerald-400 to-teal-500"></div>
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <div class="ring-2 ring-slate-100 rounded-full">
                    <x-avatar :user="$post->user" size="w-12 h-12" text="text-lg" />
                </div>
            </div>
            <div class="flex-1 min-w-0 pt-1">
                <div class="font-bold text-slate-800">{{ $post->user->name }}</div>
                <time class="text-slate-400 text-xs font-medium" datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->format('Y-m-d H:i') }}</time>
            </div>
        </div>

        <div class="mt-5">
            <p class="text-slate-800 text-base leading-relaxed whitespace-pre-line">{{ $post->content }}</p>
        </div>

        @if($post->images && count($post->images) > 0)
            <div class="mt-5 grid {{ count($post->images) === 1 ? 'grid-cols-1 max-w-lg' : (count($post->images) <= 4 ? 'grid-cols-2' : 'grid-cols-3') }} gap-2.5">
                @foreach($post->images as $image)
                    <a href="{{ asset('storage/' . $image) }}" data-fancybox="post-{{ $post->id }}" class="rounded-xl overflow-hidden ring-1 ring-slate-200/60 block {{ count($post->images) === 1 ? '' : 'aspect-square' }}">
                        <img src="{{ asset('storage/' . $image) }}" alt="" class="w-full h-full {{ count($post->images) === 1 ? '' : 'object-cover' }} hover:opacity-90 transition-opacity" loading="lazy">
                    </a>
                @endforeach
            </div>
        @endif

        @if($post->tags->count())
            <div class="flex flex-wrap gap-2 mt-5">
                @foreach($post->tags as $tag)
                    <a href="{{ route('moments', ['tag' => $tag->slug]) }}"
                       class="text-xs px-2.5 py-1 rounded-md font-medium hover:opacity-80 transition-opacity text-white shadow-sm"
                       style="background: linear-gradient(135deg, {{ $tag->color }}, {{ $tag->color }}dd);">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <button type="button" class="like-btn flex items-center gap-1.5 text-slate-500 hover:text-pink-500 transition-colors text-sm font-medium" data-type="post" data-id="{{ $post->id }}">
                    <svg class="w-5 h-5 like-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span class="like-count">{{ $post->likes_count }}</span>
                </button>
                <span class="text-sm font-medium text-slate-500">分享此动态</span>
            </div>
            <div class="social-share" data-sites="weibo,qq,wechat,twitter" data-mobile-sites="weibo,qq,wechat,twitter"
                 data-url="{{ route('post.show', $post) }}" data-title="{{ Str::limit($post->content, 60) }}">
            </div>
        </div>
    </article>

    <section class="rounded-2xl border border-stone-200/80 bg-white/90 backdrop-blur-sm p-6 sm:p-8 shadow-sm">
        <h2 class="text-lg font-bold text-stone-800">讨论 <span class="text-stone-400 font-normal ml-1">({{ $post->approvedComments->count() }})</span></h2>

        <div class="mt-6 space-y-4">
            @forelse($post->approvedComments->sortByDesc('created_at') as $comment)
                <div class="flex gap-4 border-b border-stone-100 pb-5 last:border-0 last:pb-0">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-teal-100 to-emerald-100 text-teal-700 flex items-center justify-center text-sm font-bold shrink-0 ring-2 ring-white shadow-sm">
                        {{ mb_substr($comment->nickname, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-baseline gap-2 mb-1">
                            <span class="font-bold text-stone-800 text-sm">{{ $comment->nickname }}</span>
                            <span class="text-xs font-medium text-stone-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-stone-600 text-sm leading-relaxed">{{ $comment->content }}</p>
                    </div>
                </div>
            @empty
                <div class="py-6 text-center rounded-xl bg-stone-50 border border-dashed border-stone-200">
                    <p class="text-sm text-stone-500">来发条评论吧</p>
                </div>
            @endforelse
        </div>

        <form action="{{ route('comment.store', $post) }}" method="POST" class="mt-8 pt-6 border-t border-stone-100 space-y-4">
            @csrf
            <p class="text-sm font-bold text-stone-700">发表回复</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="nickname" placeholder="昵称 *" required value="{{ old('nickname') }}"
                       class="w-full rounded-xl border-stone-200 bg-stone-50/50 text-sm px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors">
                <input type="email" name="email" placeholder="邮箱（选填）" value="{{ old('email') }}"
                       class="w-full rounded-xl border-stone-200 bg-stone-50/50 text-sm px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors">
            </div>
            <textarea name="content" rows="3" placeholder="写下你的想法..." required
                      class="w-full rounded-xl border-stone-200 bg-stone-50/50 text-sm px-4 py-3 focus:bg-white focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition-colors resize-none">{{ old('content') }}</textarea>
            <div class="text-right">
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-stone-800 px-6 py-2.5 text-sm font-medium text-white hover:bg-teal-600 shadow-sm transition-colors">
                    提交回复
                </button>
            </div>
        </form>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Fancybox.bind('[data-fancybox]', {
            groupAll: true,
        });
    });
</script>
@endsection
