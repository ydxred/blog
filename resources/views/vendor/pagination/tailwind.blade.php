@if ($paginator->hasPages())
    <nav role="navigation" aria-label="分页导航" class="flex flex-col items-center gap-4">
        <div class="inline-flex items-center gap-1.5 rounded-full border border-stone-200/80 bg-white/80 backdrop-blur-sm p-1.5 shadow-sm">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-600 hover:bg-stone-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-stone-400 font-medium">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex items-center justify-center min-w-[2.25rem] h-9 px-2 rounded-full text-sm font-bold bg-stone-800 text-white shadow-sm">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center justify-center min-w-[2.25rem] h-9 px-2 rounded-full text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900 transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-600 hover:bg-stone-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            @endif
        </div>

        <p class="text-xs font-medium text-stone-400">
            第 {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }} 页
        </p>
    </nav>
@endif
