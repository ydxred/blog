@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">文章访问记录</h2>
    <span class="text-sm text-gray-500 bg-white px-3 py-1.5 rounded-lg border border-gray-200">系统自动保留最近三个月的记录</span>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-x-auto">
    <table class="w-full text-left border-collapse whitespace-nowrap">
        <thead>
            <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-100">
                <th class="py-3 px-4 font-medium">文章</th>
                <th class="py-3 px-4 font-medium">访问者</th>
                <th class="py-3 px-4 font-medium">IP 地址</th>
                <th class="py-3 px-4 font-medium">浏览器/设备</th>
                <th class="py-3 px-4 font-medium">来源</th>
                <th class="py-3 px-4 font-medium">访问时间</th>
            </tr>
        </thead>
        <tbody class="text-sm divide-y divide-gray-100">
            @forelse($visits as $visit)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="py-3 px-4">
                        <a href="{{ route('article.show', $visit->article->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium max-w-xs truncate block" title="{{ $visit->article->title }}">
                            {{ Str::limit($visit->article->title, 20) }}
                        </a>
                    </td>
                    <td class="py-3 px-4 text-gray-600">
                        @if($visit->user)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $visit->user->name }}
                            </span>
                        @else
                            <span class="text-gray-400">访客</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-gray-600 font-mono text-xs">
                        {{ $visit->ip_address ?: '-' }}
                    </td>
                    <td class="py-3 px-4 text-gray-500 max-w-[200px] truncate" title="{{ $visit->user_agent }}">
                        {{ $visit->user_agent ?: '-' }}
                    </td>
                    <td class="py-3 px-4 text-gray-500 max-w-[150px] truncate" title="{{ $visit->referer }}">
                        @if($visit->referer)
                            <a href="{{ $visit->referer }}" target="_blank" class="hover:text-blue-500 hover:underline">{{ $visit->referer }}</a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="py-3 px-4 text-gray-500 whitespace-nowrap">
                        {{ $visit->created_at->format('Y-m-d H:i:s') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500">
                        暂无访问记录
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if($visits->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $visits->links() }}
        </div>
    @endif
</div>
@endsection
