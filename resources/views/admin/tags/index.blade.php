@extends('admin.layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">标签管理</h2>
    <a href="{{ route('admin.tags.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
        + 新建标签
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-x-auto">
    <table class="w-full whitespace-nowrap">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">排序</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">标签名</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">颜色</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">帖子数</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">操作</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($tags as $tag)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $tag->sort_order }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $tag->color }}"></span>
                            <span class="font-medium text-gray-800">{{ $tag->name }}</span>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $tag->color }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $tag->posts_count ?? $tag->posts()->count() }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.tags.edit', $tag) }}" class="text-blue-600 hover:text-blue-800 text-sm mr-3">编辑</a>
                        <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="inline" onsubmit="return confirm('确定删除此标签？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">删除</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">暂无标签，点击右上角创建</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
