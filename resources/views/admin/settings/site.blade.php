@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">站点设置</h2>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 max-w-2xl">
    <form action="{{ route('admin.settings.site.update') }}" method="POST">
        @csrf

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">网站标题</label>
            <input type="text" name="site_title" value="{{ old('site_title', $siteTitle) }}" maxlength="100"
                   placeholder="{{ config('app.name') }}"
                   class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
            @error('site_title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            <p class="mt-2 text-sm text-gray-500">显示在浏览器标签、导航栏品牌、页脚与分享卡片；首页标题即为此名称。留空则用默认值。</p>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">副标题 / 标语</label>
            <input type="text" name="site_tagline" value="{{ old('site_tagline', $siteTagline) }}" maxlength="200"
                   placeholder="{{ config('app.tagline', '记录文字与日常') }}"
                   class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
            @error('site_tagline')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            <p class="mt-2 text-sm text-gray-500">用于未单独设置标题的页面，显示为“网站标题 · 副标题”。</p>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                保存
            </button>
        </div>
    </form>
</div>
@endsection
