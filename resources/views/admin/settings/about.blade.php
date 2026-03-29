@extends('admin.layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">编辑“关于我”页面</h2>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
    <form action="{{ route('admin.settings.about.update') }}" method="POST">
        @csrf
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">页面内容 (支持 Markdown)</label>
            <textarea name="content" rows="20" class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 font-mono">{{ old('content', $content) }}</textarea>
            <p class="mt-2 text-sm text-gray-500">你可以使用 Markdown 语法来排版，前端会自动渲染为富文本格式。</p>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition">
                保存内容
            </button>
        </div>
    </form>
</div>
@endsection
