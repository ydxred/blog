{{--
    通用评论区组件
    用法：
        <x-front.comment-section
            :comments="$article->approvedComments"
            :action="route('article.comment', $article)"
            theme="indigo"
            heading="评论"
            reply-label="发表评论"
            submit-label="提交评论"
        />
--}}
@props([
    'comments',
    'action',
    'theme' => 'indigo',           // indigo | teal
    'heading' => '评论',
    'replyLabel' => '发表评论',
    'submitLabel' => '提交评论',
])

@php
    $themes = [
        'indigo' => [
            'ring'    => 'focus:ring-indigo-500/20 focus:border-indigo-500',
            'btn'     => 'hover:bg-indigo-600',
            'avatar'  => 'from-indigo-100 to-violet-100 text-indigo-700',
        ],
        'teal' => [
            'ring'    => 'focus:ring-teal-500/20 focus:border-teal-500',
            'btn'     => 'hover:bg-teal-600',
            'avatar'  => 'from-teal-100 to-emerald-100 text-teal-700',
        ],
    ];
    $t = $themes[$theme] ?? $themes['indigo'];
    $count = $comments->count();
@endphp

<section class="rounded-2xl border border-stone-200/80 bg-white/90 backdrop-blur-sm p-6 sm:p-10 shadow-sm" aria-label="评论区">
    <h2 class="text-xl font-title font-bold text-stone-800">
        {{ $heading }}
        <span class="text-stone-400 font-normal text-base ml-1">({{ $count }})</span>
    </h2>

    <ul class="mt-8 space-y-5" role="list">
        @forelse($comments->sortByDesc('created_at') as $comment)
            <li class="flex gap-4 pb-5 border-b border-stone-100 last:border-0 last:pb-0">
                <div class="h-10 w-10 shrink-0 rounded-full bg-gradient-to-br {{ $t['avatar'] }} flex items-center justify-center text-sm font-bold ring-2 ring-white shadow-sm" aria-hidden="true">
                    {{ mb_substr($comment->nickname, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-baseline gap-2 mb-1">
                        <span class="text-sm font-bold text-stone-800">{{ $comment->nickname }}</span>
                        <time class="text-xs font-medium text-stone-400" datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->diffForHumans() }}</time>
                    </div>
                    <p class="text-sm text-stone-600 leading-relaxed">{{ $comment->content }}</p>
                </div>
            </li>
        @empty
            <li class="py-8 text-center rounded-xl bg-stone-50 border border-dashed border-stone-200" role="status">
                <p class="text-sm text-stone-500">还没有人评论，来抢个沙发吧</p>
            </li>
        @endforelse
    </ul>

    <form action="{{ $action }}" method="POST" class="mt-8 pt-6 border-t border-stone-200/80 space-y-4">
        @csrf
        <p class="text-sm font-bold text-stone-700">{{ $replyLabel }}</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="comment-nickname" class="sr-only">昵称</label>
                <input id="comment-nickname" type="text" name="nickname" placeholder="昵称 *" required value="{{ old('nickname') }}"
                       autocomplete="nickname"
                       class="w-full rounded-xl border-stone-200 bg-stone-50/50 text-sm px-4 py-2.5 focus:bg-white focus:ring-2 {{ $t['ring'] }} transition-colors">
            </div>
            <div>
                <label for="comment-email" class="sr-only">邮箱（选填）</label>
                <input id="comment-email" type="email" name="email" placeholder="邮箱（选填）" value="{{ old('email') }}"
                       autocomplete="email"
                       class="w-full rounded-xl border-stone-200 bg-stone-50/50 text-sm px-4 py-2.5 focus:bg-white focus:ring-2 {{ $t['ring'] }} transition-colors">
            </div>
        </div>
        <div>
            <label for="comment-content" class="sr-only">评论内容</label>
            <textarea id="comment-content" name="content" rows="4" placeholder="写下你的想法..." required
                      class="w-full rounded-xl border-stone-200 bg-stone-50/50 text-sm px-4 py-3 focus:bg-white focus:ring-2 {{ $t['ring'] }} transition-colors resize-none">{{ old('content') }}</textarea>
        </div>
        <div class="text-right">
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-stone-800 px-6 py-2.5 text-sm font-medium text-white {{ $t['btn'] }} shadow-sm transition-colors">
                {{ $submitLabel }}
            </button>
        </div>
    </form>
</section>
