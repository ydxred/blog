<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - 管理后台</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-900" x-data="{ sidebarOpen: false }">

    <!-- Mobile overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-linear duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 bg-gray-900 bg-opacity-75 z-40 md:hidden" 
         @click="sidebarOpen = false" x-cloak></div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform transition-transform duration-300 ease-in-out md:translate-x-0 overflow-y-auto flex flex-col"
           :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
        
        <div class="flex items-center justify-center h-16 bg-gray-900 border-b border-gray-800 shrink-0">
            <h1 class="text-xl font-bold text-white">
                <a href="{{ route('admin.dashboard') }}">{{ config('app.name') }}</a>
            </h1>
        </div>
        
        <div class="px-6 py-4 shrink-0">
            <p class="text-gray-400 text-xs uppercase tracking-wider">管理后台</p>
        </div>

        <nav class="mt-2 flex-1 pb-4">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center px-6 py-3 text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                仪表盘
            </a>
            <a href="{{ route('admin.articles.index') }}"
               class="flex items-center px-6 py-3 text-sm {{ request()->routeIs('admin.articles.*') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                文章管理
            </a>
            <a href="{{ route('admin.posts.index') }}"
               class="flex items-center px-6 py-3 text-sm {{ request()->routeIs('admin.posts.*') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                帖子管理
            </a>
            <a href="{{ route('admin.tags.index') }}"
               class="flex items-center px-6 py-3 text-sm {{ request()->routeIs('admin.tags.*') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                标签管理
            </a>
            <a href="{{ route('admin.comments.index') }}"
               class="flex items-center px-6 py-3 text-sm {{ request()->routeIs('admin.comments.*') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                评论管理
            </a>
            <a href="{{ route('admin.visits.index') }}"
               class="flex items-center px-6 py-3 text-sm {{ request()->routeIs('admin.visits.*') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                访问记录
            </a>
            <a href="{{ route('admin.settings.about') }}"
               class="flex items-center px-6 py-3 text-sm {{ request()->routeIs('admin.settings.about') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                关于我
            </a>
            <a href="{{ route('admin.settings.api_wechat') }}"
               class="flex items-center px-6 py-3 text-sm {{ request()->routeIs('admin.settings.api_wechat') ? 'bg-gray-800 text-white border-l-4 border-blue-500' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                接口与微信
            </a>

            <div class="mt-4 border-t border-gray-800 pt-4">
                <a href="{{ route('home') }}" class="flex items-center px-6 py-3 text-sm text-gray-300 hover:bg-gray-800 hover:text-white">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    查看前台
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-6 py-3 text-sm text-gray-300 hover:bg-gray-800 hover:text-white text-left">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        退出登录
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="md:ml-64 flex flex-col min-h-screen">
        <!-- Mobile Header -->
        <header class="flex items-center bg-gray-900 text-white h-16 px-4 md:hidden z-30 shrink-0">
            <button @click="sidebarOpen = true" class="text-gray-300 hover:text-white focus:outline-none p-2 -ml-2">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h2 class="text-lg font-bold ml-2">
                <a href="{{ route('admin.dashboard') }}">{{ config('app.name') }}</a>
            </h2>
        </header>

        <!-- Main Page Content -->
        <main class="flex-1 p-4 md:p-8 overflow-x-hidden">
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>