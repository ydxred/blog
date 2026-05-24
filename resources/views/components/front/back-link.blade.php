@props([
    'href',
    'label',
    'theme' => 'indigo', // indigo | teal | amber
])

@php
    $themes = [
        'indigo' => 'hover:text-indigo-600 hover:ring-indigo-300',
        'teal'   => 'hover:text-teal-600 hover:ring-teal-300',
        'amber'  => 'hover:text-amber-600 hover:ring-amber-300',
    ];
    $cls = $themes[$theme] ?? $themes['indigo'];
@endphp

<a href="{{ $href }}"
   class="inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 transition-colors bg-white/50 backdrop-blur-sm px-3 py-1.5 rounded-full ring-1 ring-stone-200/80 {{ $cls }}">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    {{ $label }}
</a>
