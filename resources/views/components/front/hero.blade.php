@props([
    'title',
    'subtitle' => null,
    'theme' => 'indigo', // indigo | teal | amber
])

@php
    $themes = [
        'indigo' => ['from' => 'from-indigo-50/50', 'h' => 'text-indigo-900/90'],
        'teal'   => ['from' => 'from-teal-50/50',   'h' => 'text-teal-900/90'],
        'amber'  => ['from' => 'from-amber-50/50',  'h' => 'text-amber-900/90'],
        'blue'   => ['from' => 'from-blue-50/50',   'h' => 'text-blue-900/90'],
    ];
    $t = $themes[$theme] ?? $themes['indigo'];
@endphp

<section class="relative overflow-hidden border-b border-slate-200/60 bg-white/40 backdrop-blur-sm">
    <div class="absolute inset-0 bg-gradient-to-b {{ $t['from'] }} to-transparent"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-5 py-10 sm:py-14 relative z-10">
        <h1 class="text-3xl sm:text-4xl font-title font-bold {{ $t['h'] }} tracking-tight">{{ $title }}</h1>
        @if($subtitle)
            <p class="mt-3 text-slate-500 sm:text-lg max-w-xl leading-relaxed">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>
</section>
