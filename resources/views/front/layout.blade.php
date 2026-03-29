<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;600;700&family=Noto+Serif+SC:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/social-share.js@1.0.16/dist/css/share.min.css">
    @yield('styles')
    <style>
        [x-cloak] { display: none !important; }
        .social-share .icon-wechat { position: relative; }
        emoji-picker {
            --border-radius: 12px;
            --background: #ffffff;
            --border-color: #e7e5e4;
            --input-border-color: #e7e5e4;
            --indicator-color: #6366f1;
            --outline-color: #6366f1;
            height: 300px;
        }
        body { font-family: 'Noto Sans SC', system-ui, -apple-system, sans-serif; }
        .font-title { font-family: 'Noto Serif SC', 'Noto Sans SC', serif; }
        .bg-page {
            background-color: #f8fafc;
            background-image:
                radial-gradient(at 0% 0%, hsla(253,16%,7%,0.05) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.05) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.05) 0, transparent 50%);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-page text-slate-800 antialiased">
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/90 backdrop-blur-md shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-5 h-[3.25rem] sm:h-14 flex items-center justify-between">
            <div class="flex items-center gap-5 sm:gap-8 min-w-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-semibold text-slate-700 truncate group">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 text-white shadow-sm transition-shadow group-hover:shadow-md">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </span>
                    <span class="truncate group-hover:text-indigo-600 transition-colors">{{ config('app.name') }}</span>
                </a>
                <nav class="hidden sm:flex items-center gap-1.5 text-sm">
                    <a href="{{ route('home') }}"
                       class="px-4 py-2 rounded-full font-medium transition-all {{ request()->routeIs('home') || request()->routeIs('article.*') ? 'bg-blue-50/80 text-blue-700 ring-1 ring-blue-200/60' : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/50' }}">博客</a>
                    <a href="{{ route('moments') }}"
                       class="px-4 py-2 rounded-full font-medium transition-all {{ request()->routeIs('moments') || request()->routeIs('post.*') ? 'bg-teal-50/80 text-teal-700 ring-1 ring-teal-200/60' : 'text-slate-600 hover:text-teal-600 hover:bg-teal-50/50' }}">动态</a>
                    <a href="{{ route('about') }}"
                       class="px-4 py-2 rounded-full font-medium transition-all {{ request()->routeIs('about') ? 'bg-amber-50/80 text-amber-700 ring-1 ring-amber-200/60' : 'text-slate-600 hover:text-amber-600 hover:bg-amber-50/50' }}">关于我</a>
                </nav>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <nav class="flex sm:hidden items-center gap-0.5 text-[13px]">
                    <a href="{{ route('home') }}" class="px-2 py-1.5 rounded-lg transition-colors {{ request()->routeIs('home') || request()->routeIs('article.*') ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-500 hover:text-blue-600' }}">博客</a>
                    <a href="{{ route('moments') }}" class="px-2 py-1.5 rounded-lg transition-colors {{ request()->routeIs('moments') || request()->routeIs('post.*') ? 'bg-teal-50 text-teal-700 font-semibold' : 'text-slate-500 hover:text-teal-600' }}">动态</a>
                    <a href="{{ route('about') }}" class="px-2 py-1.5 rounded-lg transition-colors {{ request()->routeIs('about') ? 'bg-amber-50 text-amber-700 font-semibold' : 'text-slate-500 hover:text-amber-600' }}">关于我</a>
                </nav>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline text-xs text-stone-400 hover:text-violet-600 transition-colors">后台</a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="flex items-center ring-2 ring-amber-200/70 rounded-full">
                        <x-avatar :user="auth()->user()" size="w-8 h-8" text="text-xs" />
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-stone-400 hover:text-red-600">退出</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium bg-gradient-to-r from-indigo-600 to-violet-600 text-white px-3.5 py-1.5 rounded-full hover:from-indigo-500 hover:to-violet-500 shadow-md shadow-indigo-500/20 transition-all">登录</a>
                @endauth
            </div>
        </div>
    </header>

    @yield('hero')

    <main class="max-w-5xl mx-auto w-full px-4 sm:px-5 py-8 sm:py-10 flex-1 relative z-10">
        @if(session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200/80 bg-emerald-50/90 text-emerald-900 px-4 py-3 text-sm shadow-sm backdrop-blur-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-200/80 bg-red-50/90 text-red-900 px-4 py-3 text-sm shadow-sm backdrop-blur-sm">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="mt-auto relative z-10">
        <div class="h-px w-full bg-gradient-to-r from-transparent via-slate-300/60 to-transparent"></div>
        <div class="border-t border-slate-200/80 bg-white/60 backdrop-blur-md py-10">
            <div class="max-w-5xl mx-auto px-4 sm:px-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div>
                        <p class="font-title text-lg text-indigo-900/80">{{ config('app.name') }}</p>
                        <p class="text-sm text-slate-500 mt-1">记录文字与日常</p>
                    </div>
                    <nav class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                        <a href="{{ route('home') }}" class="text-slate-500 hover:text-blue-600 transition-colors">文章</a>
                        <a href="{{ route('moments') }}" class="text-slate-500 hover:text-teal-600 transition-colors">动态</a>
                        <a href="{{ route('about') }}" class="text-slate-500 hover:text-amber-600 transition-colors">关于我</a>
                        @guest
                            <a href="{{ route('login') }}" class="text-slate-500 hover:text-indigo-600 transition-colors">登录</a>
                        @endguest
                        @auth
                            <a href="{{ route('profile.edit') }}" class="text-slate-500 hover:text-indigo-600 transition-colors">资料</a>
                        @endauth
                    </nav>
                </div>
                <div class="mt-8 pt-6 border-t border-slate-200/60 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-400">
                    <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
                    <p><a href="https://laravel.com" class="hover:text-blue-500 transition-colors" rel="noopener">Laravel</a></p>
                </div>
            </div>
        </div>
    </footer>

    <button type="button" id="back-to-top" aria-label="回到顶部"
            class="fixed bottom-6 right-4 sm:right-6 z-40 flex h-11 w-11 items-center justify-center rounded-full bg-white text-blue-600 shadow-lg shadow-slate-300/50 ring-1 ring-slate-200 opacity-0 pointer-events-none translate-y-2 transition-all duration-300 hover:bg-blue-50 hover:ring-blue-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
    </button>

    <script>
        (function () {
            var btn = document.getElementById('back-to-top');
            if (!btn) return;
            function toggle() {
                if (window.scrollY > 320) {
                    btn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
                } else {
                    btn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
                }
            }
            window.addEventListener('scroll', toggle, { passive: true });
            toggle();
            btn.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/social-share.js@1.0.16/dist/js/social-share.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@1/index.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            
            // Fetch initial like status
            fetch('/likes/status', { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    document.querySelectorAll('.like-btn').forEach(btn => {
                        const type = btn.dataset.type;
                        const id = parseInt(btn.dataset.id);
                        if (data[type + 's'] && Object.values(data[type + 's']).includes(id)) {
                            btn.classList.add('text-pink-500');
                            btn.classList.remove('text-slate-400', 'text-slate-500');
                            if(btn.classList.contains('ring-1')) btn.classList.add('ring-pink-200');
                            btn.querySelector('.like-icon').classList.add('fill-current');
                        }
                    });
                })
                .catch(err => console.log('Likes status error', err));

            // Handle like click
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.like-btn');
                if (!btn) return;
                
                e.preventDefault();
                btn.disabled = true;
                const type = btn.dataset.type;
                const id = btn.dataset.id;
                const icon = btn.querySelector('.like-icon');
                const countEl = btn.querySelector('.like-count');
                
                fetch(`/like/${type}/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.liked) {
                        btn.classList.add('text-pink-500');
                        btn.classList.remove('text-slate-400', 'text-slate-500');
                        if(btn.classList.contains('ring-1')) btn.classList.add('ring-pink-200');
                        icon.classList.add('fill-current');
                    } else {
                        btn.classList.remove('text-pink-500');
                        if(btn.classList.contains('ring-1')) btn.classList.remove('ring-pink-200');
                        icon.classList.remove('fill-current');
                        btn.classList.add(type === 'article' ? 'text-slate-500' : 'text-slate-400');
                    }
                    countEl.textContent = data.likes_count > 0 ? data.likes_count : (type === 'post' && !btn.classList.contains('px-4') ? '喜欢' : '0');
                })
                .finally(() => {
                    btn.disabled = false;
                });
            });
        });
    </script>
    @yield('scripts')
</body>
</html>