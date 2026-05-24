@props([
    'tag',
    'href' => null,
    'size' => 'md',     // sm | md | lg
    'active' => false,  // 给"标签筛选条"那种用
])

@php
    $sizes = [
        'sm' => 'text-xs px-2.5 py-1',
        'md' => 'text-xs px-3 py-1',
        'lg' => 'text-sm px-4 py-1.5',
    ];
    $cls = $sizes[$size] ?? $sizes['md'];
@endphp

@if($href)
    <a href="{{ $href }}"
       @class([
           'inline-flex items-center rounded-full font-medium transition-all',
           $cls,
           'tag-pill shadow-sm hover:opacity-80' => $active || $size !== 'lg',
           'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-slate-300 hover:bg-slate-50' => $size === 'lg' && !$active,
       ])
       @if($active || $size !== 'lg') style="--tag-color: {{ $tag->color }};" @endif>
        {{ $slot->isNotEmpty() ? $slot : $tag->name }}
    </a>
@else
    <span class="inline-flex items-center rounded-full font-medium tag-pill shadow-sm {{ $cls }}"
          style="--tag-color: {{ $tag->color }};">
        {{ $slot->isNotEmpty() ? $slot : $tag->name }}
    </span>
@endif
