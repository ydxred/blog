{{--
    点赞按钮
    用法：
        <x-front.like-button type="article" :id="$article->id" :count="$article->likes_count" />
        <x-front.like-button type="post" :id="$post->id" :count="$post->likes_count" inline />
--}}
@props([
    'type',                  // article | post
    'id',
    'count' => 0,
    'inline' => false,       // 紧凑模式（动态详情用）
])

@if($inline)
    <button type="button"
            class="like-btn flex items-center gap-1.5 text-slate-500 hover:text-pink-500 transition-colors text-sm font-medium"
            data-type="{{ $type }}" data-id="{{ $id }}"
            aria-pressed="false" aria-label="点赞">
        <svg class="w-5 h-5 like-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <span class="like-count">{{ $count }}</span>
    </button>
@else
    <button type="button"
            class="like-btn inline-flex items-center gap-2 px-4 py-2 rounded-full ring-1 ring-slate-200 bg-white/50 text-slate-500 hover:text-pink-500 hover:ring-pink-200 transition-all font-medium text-sm"
            data-type="{{ $type }}" data-id="{{ $id }}"
            aria-pressed="false" aria-label="点赞">
        <svg class="w-5 h-5 like-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <span class="like-count">{{ $count }}</span>
    </button>
@endif
