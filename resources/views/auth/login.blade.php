@extends('front.layout')

@section('content')
<div class="max-w-sm mx-auto mt-16 px-4">
    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-800">欢迎回来</h2>
        <p class="text-sm text-gray-400 mt-1">登录你的账号</p>
    </div>

    <form method="POST" action="{{ route('login') }}" id="loginForm"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">邮箱</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="请输入邮箱">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">密码</label>
            <input type="password" name="password" required
                   class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="请输入密码">
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-500">记住我</span>
        </label>

        <button type="submit" id="submitBtn"
                class="w-full bg-blue-500 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-blue-600 transition">
            登录
        </button>
    </form>
</div>
@endsection
