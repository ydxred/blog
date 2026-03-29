<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">帖子管理</h2>
    <form action="<?php echo e(route('admin.posts.index')); ?>" method="GET" class="flex gap-2">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="搜索内容..."
               class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
        <button type="submit" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 text-sm">搜索</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-x-auto">
    <table class="w-full whitespace-nowrap">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">内容</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">作者</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">标签</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">状态</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">时间</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">操作</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 max-w-xs">
                        <p class="text-sm text-gray-800 truncate"><?php echo e(Str::limit($post->content, 60)); ?></p>
                        <?php if($post->images): ?>
                            <span class="text-xs text-gray-400"><?php echo e(count($post->images)); ?>张图片</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($post->user->name); ?></td>
                    <td class="px-6 py-4">
                        <?php $__currentLoopData = $post->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="text-xs px-2 py-0.5 rounded-full text-white" style="background-color: <?php echo e($tag->color); ?>"><?php echo e($tag->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($post->status === 'published'): ?>
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">已发布</span>
                        <?php else: ?>
                            <span class="text-xs px-2 py-1 rounded-full bg-red-100 text-red-700">已隐藏</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400"><?php echo e($post->created_at->format('m-d H:i')); ?></td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <form action="<?php echo e(route('admin.posts.toggle', $post)); ?>" method="POST" class="inline">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm mr-2">
                                <?php echo e($post->status === 'published' ? '隐藏' : '显示'); ?>

                            </button>
                        </form>
                        <form action="<?php echo e(route('admin.posts.destroy', $post)); ?>" method="POST" class="inline" onsubmit="return confirm('确定删除？')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">删除</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">暂无帖子</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <?php echo e($posts->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/ydxred.com/resources/views/admin/posts/index.blade.php ENDPATH**/ ?>