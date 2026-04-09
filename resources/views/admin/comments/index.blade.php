@extends('admin.layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">评论管理</h2>
    <div class="flex gap-2">
        <a href="{{ route('admin.comments.index') }}"
           class="px-3 py-1.5 rounded-lg text-sm {{ !request('status') ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">全部</a>
        <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
           class="px-3 py-1.5 rounded-lg text-sm {{ request('status') === 'pending' ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">待审核</a>
        <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}"
           class="px-3 py-1.5 rounded-lg text-sm {{ request('status') === 'approved' ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">已通过</a>
        <a href="{{ route('admin.comments.index', ['status' => 'rejected']) }}"
           class="px-3 py-1.5 rounded-lg text-sm {{ request('status') === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">已拒绝</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-x-auto">
    <table class="w-full whitespace-nowrap">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">评论人</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">内容</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP与设备</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">来源</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">状态</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">时间</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">操作</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($comments as $comment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-800">{{ $comment->nickname }}</div>
                        <div class="text-xs text-gray-400">{{ $comment->email }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 max-w-xs truncate" title="{{ $comment->content }}">{{ Str::limit($comment->content, 60) }}</td>
                    <td class="px-6 py-4">
                        <div class="text-xs text-gray-600 font-mono">{{ $comment->ip_address ?? '未知 IP' }}</div>
                        <div class="text-xs text-gray-400 mt-1 max-w-[12rem] truncate" title="{{ $comment->user_agent }}">
                            {{ Str::limit($comment->user_agent ?? '未知设备', 30) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($comment->commentable)
                            @if($comment->commentable_type === 'App\\Models\\Article')
                                <span class="text-xs px-2 py-0.5 rounded bg-indigo-100 text-indigo-700">文章</span>
                                <a href="{{ route('article.show', $comment->commentable->slug) }}" class="text-blue-600 hover:underline text-xs ml-1" target="_blank">
                                    {{ Str::limit($comment->commentable->title, 20) }}
                                </a>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded bg-blue-100 text-blue-700">帖子</span>
                                <a href="{{ route('post.show', $comment->commentable) }}" class="text-blue-600 hover:underline text-xs ml-1" target="_blank">
                                    {{ Str::limit($comment->commentable->content, 20) }}
                                </a>
                            @endif
                        @else
                            <span class="text-gray-400 text-xs">已删除</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($comment->status === 'pending')
                            <span class="text-xs px-2 py-1 rounded-full bg-orange-100 text-orange-700">待审核</span>
                        @elseif($comment->status === 'approved')
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">已通过</span>
                        @else
                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700">已拒绝</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400">{{ $comment->created_at->format('m-d H:i') }}</td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        @if($comment->status !== 'approved')
                            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-green-600 hover:text-green-800 text-sm mr-2">通过</button>
                            </form>
                        @endif
                        @if($comment->status !== 'rejected')
                            <form action="{{ route('admin.comments.reject', $comment) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-orange-600 hover:text-orange-800 text-sm mr-2">拒绝</button>
                            </form>
                        @endif
                        <form action="{{ route('admin.comments.destroy', $comment) }}" method="POST" class="inline" onsubmit="return confirm('确定删除？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">删除</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-400">暂无评论</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $comments->appends(request()->query())->links() }}
</div>
@endsection
