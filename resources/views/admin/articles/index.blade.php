@extends('admin.layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">文章管理</h2>
    <div class="flex gap-3">
        <form action="{{ route('admin.articles.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="搜索标题..."
                   class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
            <select name="status" class="border-gray-300 rounded-lg shadow-sm text-sm" onchange="this.form.submit()">
                <option value="">全部状态</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>已发布</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>草稿</option>
            </select>
            <button type="submit" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 text-sm">搜索</button>
        </form>
        <a href="{{ route('admin.articles.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm whitespace-nowrap">
            + 写文章
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-x-auto">
    <table class="w-full whitespace-nowrap">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">标题</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">作者</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">标签</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">状态</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">阅读</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">发布时间</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">操作</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($articles as $article)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500 font-mono">{{ $article->id }}</td>
                    <td class="px-6 py-4 max-w-xs">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $article->title }}</p>
                        @if($article->cover_image)
                            <span class="text-xs text-gray-400">有封面图</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $article->user->name }}</td>
                    <td class="px-6 py-4">
                        @foreach($article->tags as $tag)
                            <span class="text-xs px-2 py-0.5 rounded-full text-white" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4">
                        @if($article->status === 'published')
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">已发布</span>
                        @else
                            <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">草稿</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $article->views_count }}</td>
                    <td class="px-6 py-4 text-sm text-gray-400">
                        {{ $article->published_at ? $article->published_at->format('Y-m-d H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.articles.edit', $article) }}" class="text-blue-600 hover:text-blue-800 text-sm mr-2">编辑</a>
                        @if($article->status === 'published')
                            <a href="{{ route('article.show', $article->slug) }}" target="_blank" class="text-gray-500 hover:text-gray-700 text-sm mr-2">查看</a>
                        @endif
                        <form action="{{ route('admin.wechat.sync', $article) }}" method="POST" class="inline" onsubmit="return confirm('确定要将此文章同步到微信公众号草稿箱吗？')">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-800 text-sm mr-2">同步微信</button>
                        </form>
                        <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="inline" onsubmit="return confirm('确定删除此文章？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">删除</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">暂无文章，点击右上角开始写作</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $articles->appends(request()->query())->links() }}
</div>
@endsection
