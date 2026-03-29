@extends('front.layout')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
@endsection

@section('hero')
<div class="relative overflow-hidden border-b border-slate-200/60 bg-white/40 backdrop-blur-sm">
    <div class="absolute inset-0 bg-gradient-to-b from-teal-50/50 to-transparent"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-5 py-10 sm:py-14 relative z-10">
        <h1 class="text-3xl sm:text-4xl font-title font-bold text-teal-900/90 tracking-tight">碎碎念与日常</h1>
        <p class="mt-3 text-slate-500 sm:text-lg max-w-xl leading-relaxed">捕捉瞬间的灵感、生活的切片以及那些无需长篇大论的思绪。</p>
    </div>
</div>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    @auth
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-7 mb-10 shadow-sm ring-1 ring-slate-200/50" x-data="postForm()">
        <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data" x-ref="form">
            @csrf
            <div class="flex gap-4 sm:gap-5">
                <div class="flex-shrink-0">
                    <x-avatar :user="auth()->user()" />
                </div>
                <div class="flex-1 min-w-0 pt-1">
                    <textarea name="content" rows="3" x-model="content" x-ref="textarea"
                              class="w-full border-0 resize-none focus:ring-0 text-slate-800 placeholder-slate-400 text-base p-0 bg-transparent"
                              placeholder="此刻在想什么？"></textarea>

                    <div x-show="previews.length > 0" class="flex flex-wrap gap-2.5 mt-3">
                        <template x-for="(preview, index) in previews" :key="index">
                            <div class="relative w-24 h-24 rounded-xl overflow-hidden ring-1 ring-slate-200 group">
                                <img :src="preview" class="w-full h-full object-cover" alt="">
                                <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <button type="button" @click="removeImage(index)"
                                        class="absolute top-1 right-1 w-6 h-6 bg-black/50 backdrop-blur-sm rounded-full text-white text-xs flex items-center justify-center hover:bg-red-500 transition-colors">&times;</button>
                            </div>
                        </template>
                    </div>

                    <input type="file" name="images[]" multiple x-ref="fileInput" class="hidden">

                    <div x-show="showTags" x-cloak class="flex flex-wrap gap-2 mt-4">
                        @foreach(\App\Models\Tag::orderBy('sort_order')->get() as $tag)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="hidden peer">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium border transition-all
                                             peer-checked:text-white peer-checked:border-transparent peer-checked:shadow-sm
                                             text-slate-600 border-slate-200 hover:border-slate-300 bg-slate-50"
                                      @click="$nextTick(() => { $el.style.backgroundColor = $el.previousElementSibling.checked ? '{{ $tag->color }}' : ''; $el.style.borderColor = $el.previousElementSibling.checked ? '{{ $tag->color }}' : ''; $el.style.color = $el.previousElementSibling.checked ? 'white' : ''; })">
                                    {{ $tag->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>

                    <div x-show="showEmoji" x-cloak x-transition class="mt-4" x-ref="emojiPanel">
                        <emoji-picker class="w-full shadow-sm rounded-xl overflow-hidden border border-slate-200"></emoji-picker>
                    </div>

                    <div class="flex items-center justify-between mt-5 pt-4 border-t border-slate-100">
                        <div class="flex gap-1.5">
                            <label class="cursor-pointer p-2 rounded-lg text-slate-500 hover:bg-teal-50 hover:text-teal-600 transition-colors" title="添加图片">
                                <input type="file" multiple accept="image/*" class="hidden" @change="handleImages($event)">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </label>
                            <button type="button" @click="showEmoji = !showEmoji; showTags = false; if(showEmoji) initPicker()"
                                    class="p-2 rounded-lg text-slate-500 hover:bg-teal-50 hover:text-teal-600 transition-colors"
                                    :class="showEmoji ? 'text-teal-600 bg-teal-50' : ''" title="添加表情">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </button>
                            <button type="button" @click="showTags = !showTags; showEmoji = false"
                                    class="p-2 rounded-lg text-slate-500 hover:bg-teal-50 hover:text-teal-600 transition-colors"
                                    :class="showTags ? 'text-teal-600 bg-teal-50' : ''" title="添加标签">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            </button>
                        </div>
                        <button type="submit" :disabled="!content.trim()"
                                class="bg-gradient-to-r from-emerald-500 to-teal-500 text-white px-6 py-2 rounded-full text-sm font-medium shadow-sm shadow-teal-500/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed hover:from-emerald-400 hover:to-teal-400">
                            发布动态
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    @endauth

    @if($tags->count())
    <div class="flex flex-wrap gap-2.5 mb-8">
        <a href="{{ route('moments') }}"
           class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium transition-all {{ !$currentTag ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-md shadow-teal-500/20' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-slate-300 hover:bg-slate-50' }}">
            全部
        </a>
        @foreach($tags as $tag)
            <a href="{{ route('moments', ['tag' => $tag->slug]) }}"
               class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium transition-all {{ $currentTag === $tag->slug ? 'text-white shadow-md' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-slate-300 hover:bg-slate-50' }}"
               @if($currentTag === $tag->slug) style="background: linear-gradient(135deg, {{ $tag->color }}, {{ $tag->color }}dd); box-shadow: 0 4px 14px -2px {{ $tag->color }}66;" @endif>
                {{ $tag->name }}
            </a>
        @endforeach
    </div>
    @endif

    <div id="posts-container" class="space-y-6 relative before:absolute before:inset-y-0 before:left-7 sm:before:left-[2.1rem] before:w-px before:bg-slate-200/80 before:-z-10">
        @if($posts->count())
            @include('front.partials.post-list')
        @else
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white/50 backdrop-blur-sm py-16 text-center">
                <p class="text-slate-500 text-sm">暂无动态</p>
                <p class="text-slate-400 text-xs mt-2">登录后可以发布第一条</p>
            </div>
        @endif
    </div>

    @if($posts->hasPages())
    <div id="load-more-wrapper" class="mt-10 flex flex-col items-center gap-3">
        @if($posts->hasMorePages())
            <button id="load-more-btn"
                    data-next-url="{{ $posts->appends(request()->query())->nextPageUrl() }}"
                    class="px-8 py-2.5 rounded-full text-sm font-medium ring-1 ring-stone-200 bg-white text-stone-700 hover:ring-teal-300 hover:text-teal-700 hover:shadow-md hover:shadow-teal-500/10 transition-all flex items-center gap-2">
                <span id="load-more-text">加载更多</span>
                <svg id="load-more-spinner" class="hidden w-4 h-4 animate-spin text-teal-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </button>
        @endif
        <p id="posts-stats" class="text-xs text-stone-400 mt-2">
            已显示 <span id="shown-count">{{ $posts->count() }}</span> / {{ $posts->total() }} 条
        </p>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Fancybox.bind('[data-fancybox]', {
                groupAll: true,
            });
        });
        function postForm() {
            return {
                content: '',
                showTags: false,
                showEmoji: false,
                pickerReady: false,
                previews: [],
                files: [],
                handleImages(event) {
                    const newFiles = Array.from(event.target.files);
                    for (let i = 0; i < newFiles.length && this.files.length < 15; i++) {
                        const file = newFiles[i];
                        this.files.push(file);
                        const reader = new FileReader();
                        reader.onload = (e) => this.previews.push(e.target.result);
                        reader.readAsDataURL(file);
                    }
                    this.syncFileInput();
                    event.target.value = '';
                },
                removeImage(index) {
                    this.previews.splice(index, 1);
                    this.files.splice(index, 1);
                    this.syncFileInput();
                },
                syncFileInput() {
                    const dt = new DataTransfer();
                    this.files.forEach(f => dt.items.add(f));
                    this.$refs.fileInput.files = dt.files;
                },
                initPicker() {
                    if (this.pickerReady) return;
                    this.$nextTick(() => {
                        const picker = this.$refs.emojiPanel.querySelector('emoji-picker');
                        if (picker) {
                            picker.addEventListener('emoji-click', (e) => {
                                const emoji = e.detail.unicode;
                                const ta = this.$refs.textarea;
                                const start = ta.selectionStart;
                                const end = ta.selectionEnd;
                                this.content = this.content.substring(0, start) + emoji + this.content.substring(end);
                                this.$nextTick(() => {
                                    const pos = start + emoji.length;
                                    ta.focus();
                                    ta.setSelectionRange(pos, pos);
                                });
                            });
                            this.pickerReady = true;
                        }
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('load-more-btn');
            if (!btn) return;

            const container = document.getElementById('posts-container');
            const shownCount = document.getElementById('shown-count');
            const textEl = document.getElementById('load-more-text');
            const spinner = document.getElementById('load-more-spinner');

            btn.addEventListener('click', function() {
                const url = btn.dataset.nextUrl;
                if (!url) return;

                btn.disabled = true;
                textEl.textContent = '加载中…';
                spinner.classList.remove('hidden');

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(data => {
                    const temp = document.createElement('div');
                    temp.innerHTML = data.html;
                    while (temp.firstElementChild) {
                        container.appendChild(temp.firstElementChild);
                    }

                    if (shownCount) {
                        shownCount.textContent = container.querySelectorAll(':scope > article').length;
                    }

                    if (data.next_page_url) {
                        btn.dataset.nextUrl = data.next_page_url;
                        btn.disabled = false;
                        textEl.textContent = '加载更多';
                        spinner.classList.add('hidden');
                    } else {
                        btn.remove();
                    }
                })
                .catch(() => {
                    btn.disabled = false;
                    textEl.textContent = '加载失败，重试';
                    spinner.classList.add('hidden');
                });
            });
        });
    </script>
</div>
@endsection
