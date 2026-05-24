@extends('front.layout')

@section('title', '动态 · ' . Str::limit($post->content, 30))
@section('description', Str::limit(strip_tags($post->content), 160))
@section('og_type', 'article')
@if($post->images && count($post->images) > 0)
    @section('og_image', asset('storage/' . $post->images[0]))
@endif
@section('canonical', route('post.show', $post))

@push('scripts')
    @vite(['resources/js/moments.js', 'resources/js/share.js'])
    <script src="{{ asset('lib/social-share.min.js') }}" defer></script>
@endpush

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <x-front.back-link :href="route('moments')" label="返回动态" theme="teal" />
    </div>

    <article class="rounded-2xl border border-slate-200/80 bg-white/90 backdrop-blur-sm p-6 sm:p-8 shadow-sm mb-8 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-emerald-400 to-teal-500" aria-hidden="true"></div>
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
                    <a href="{{ asset('storage/' . $image) }}" data-fancybox="post-{{ $post->id }}"
                       class="rounded-xl overflow-hidden ring-1 ring-slate-200/60 block {{ count($post->images) === 1 ? '' : 'aspect-square' }}"
                       aria-label="查看大图">
                        <img src="{{ asset('storage/' . $image) }}" alt="动态配图" class="w-full h-full {{ count($post->images) === 1 ? '' : 'object-cover' }} hover:opacity-90 transition-opacity" loading="lazy" decoding="async">
                    </a>
                @endforeach
            </div>
        @endif

        @if($post->tags->count())
            <div class="flex flex-wrap gap-2 mt-5">
                @foreach($post->tags as $tag)
                    <x-front.tag-pill :tag="$tag" :href="route('moments', ['tag' => $tag->slug])">#{{ $tag->name }}</x-front.tag-pill>
                @endforeach
            </div>
        @endif

        <div class="mt-6 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <x-front.like-button type="post" :id="$post->id" :count="$post->likes_count" inline />
                <span class="text-sm font-medium text-slate-500">分享此动态</span>
            </div>
            <div class="social-share" data-sites="weibo,qq,wechat,twitter" data-mobile-sites="weibo,qq,wechat,twitter"
                 data-url="{{ route('post.show', $post) }}" data-title="{{ Str::limit($post->content, 60) }}">
            </div>
        </div>
    </article>

    <x-front.comment-section
        :comments="$post->approvedComments"
        :action="route('comment.store', $post)"
        theme="teal"
        heading="讨论"
        reply-label="发表回复"
        submit-label="提交回复" />
</div>
@endsection
