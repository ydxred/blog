<?php $__env->startSection('content'); ?>
<h2 class="text-2xl font-bold text-gray-800 mb-6">仪表盘</h2>

<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-3xl font-bold text-indigo-600"><?php echo e($articles_count); ?></div>
        <div class="text-gray-500 mt-1">文章总数</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-3xl font-bold text-blue-600"><?php echo e($posts_count); ?></div>
        <div class="text-gray-500 mt-1">帖子总数</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-3xl font-bold text-green-600"><?php echo e($tags_count); ?></div>
        <div class="text-gray-500 mt-1">标签数量</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-3xl font-bold text-purple-600"><?php echo e($comments_count); ?></div>
        <div class="text-gray-500 mt-1">评论总数</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="text-3xl font-bold text-orange-600"><?php echo e($pending_comments); ?></div>
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
        <?php $__empty_1 = true; $__currentLoopData = $recent_articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-start gap-3 py-3 <?php echo e(!$loop->last ? 'border-b border-gray-100' : ''); ?>">
                <div class="flex-1 min-w-0">
                    <p class="text-gray-800 font-medium truncate"><?php echo e($article->title); ?></p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-400"><?php echo e($article->user->name); ?></span>
                        <span class="text-xs text-gray-300">&middot;</span>
                        <span class="text-xs text-gray-400"><?php echo e($article->created_at->diffForHumans()); ?></span>
                        <?php if($article->status === 'draft'): ?>
                            <span class="text-xs px-1.5 py-0.5 rounded bg-yellow-100 text-yellow-700">草稿</span>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="<?php echo e(route('admin.articles.edit', $article)); ?>" class="text-blue-600 text-xs hover:underline shrink-0">编辑</a>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-gray-400 text-center py-8">暂无文章</p>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">最新帖子</h3>
        <?php $__empty_1 = true; $__currentLoopData = $recent_posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="flex items-start gap-3 py-3 <?php echo e(!$loop->last ? 'border-b border-gray-100' : ''); ?>">
                <div class="flex-1 min-w-0">
                    <p class="text-gray-800 truncate"><?php echo e(Str::limit($post->content, 80)); ?></p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-400"><?php echo e($post->user->name); ?></span>
                        <span class="text-xs text-gray-300">&middot;</span>
                        <span class="text-xs text-gray-400"><?php echo e($post->created_at->diffForHumans()); ?></span>
                        <?php $__currentLoopData = $post->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="text-xs px-2 py-0.5 rounded-full text-white" style="background-color: <?php echo e($tag->color); ?>"><?php echo e($tag->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-gray-400 text-center py-8">暂无帖子</p>
        <?php endif; ?>
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
                    labels: <?php echo $chartLabels; ?>,
                    datasets: [
                        {
                            label: 'PV (浏览量)',
                            data: <?php echo $chartPV; ?>,
                            borderColor: 'rgb(79, 70, 229)',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'UV (独立访客)',
                            data: <?php echo $chartUV; ?>,
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/ydxred.com/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>