@extends('admin.layouts.app')

@section('content')
<h2 class="text-2xl font-bold text-gray-800 mb-6">仪表盘</h2>

<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-3xl font-bold text-indigo-600">{{ $articles_count }}</div>
        <div class="text-gray-500 mt-1">文章总数</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-3xl font-bold text-blue-600">{{ $posts_count }}</div>
        <div class="text-gray-500 mt-1">帖子总数</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-3xl font-bold text-green-600">{{ $tags_count }}</div>
        <div class="text-gray-500 mt-1">标签数量</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-3xl font-bold text-purple-600">{{ $comments_count }}</div>
        <div class="text-gray-500 mt-1">评论总数</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-3xl font-bold text-orange-600">{{ $pending_comments }}</div>
        <div class="text-gray-500 mt-1">待审核评论</div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">近 7 天文章访问趋势</h3>
    <div class="relative h-64">
        <canvas id="visitChart"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">最新文章</h3>
        @forelse($recent_articles as $article)
            <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <div class="flex-1 min-w-0">
                    <p class="text-gray-800 font-medium truncate">{{ $article->title }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-400">{{ $article->user->name }}</span>
                        <span class="text-xs text-gray-300">&middot;</span>
                        <span class="text-xs text-gray-400">{{ $article->created_at->diffForHumans() }}</span>
                        @if($article->status === 'draft')
                            <span class="text-xs px-1.5 py-0.5 rounded bg-yellow-100 text-yellow-700">草稿</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('admin.articles.edit', $article) }}" class="text-blue-600 text-xs hover:underline shrink-0">编辑</a>
            </div>
        @empty
            <p class="text-gray-400 text-center py-8">暂无文章</p>
        @endforelse
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">最新帖子</h3>
        @forelse($recent_posts as $post)
            <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <div class="flex-1 min-w-0">
                    <p class="text-gray-800 truncate">{{ Str::limit($post->content, 80) }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-400">{{ $post->user->name }}</span>
                        <span class="text-xs text-gray-300">&middot;</span>
                        <span class="text-xs text-gray-400">{{ $post->created_at->diffForHumans() }}</span>
                        @foreach($post->tags as $tag)
                            <span class="text-xs px-2 py-0.5 rounded-full text-white" style="background-color: {{ $tag->color }}">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-center py-8">暂无帖子</p>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('visitChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! $chartLabels !!},
                    datasets: [
                        {
                            label: 'PV (浏览量)',
                            data: {!! $chartPV !!},
                            borderColor: 'rgb(79, 70, 229)',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'UV (独立访客)',
                            data: {!! $chartUV !!},
                            borderColor: 'rgb(16, 185, 129)',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' }
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });
        }
    });
</script>
@endsection
