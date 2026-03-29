<?php $__env->startSection('content'); ?>
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">文章管理</h2>
    <div class="flex gap-3">
        <form action="<?php echo e(route('admin.articles.index')); ?>" method="GET" class="flex gap-2">
            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="搜索标题..."
                   class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
            <select name="status" class="border-gray-300 rounded-lg shadow-sm text-sm" onchange="this.form.submit()">
                <option value="">全部状态</option>
                <option value="published" <?php echo e(request('status') === 'published' ? 'selected' : ''); ?>>已发布</option>
                <option value="draft" <?php echo e(request('status') === 'draft' ? 'selected' : ''); ?>>草稿</option>
            </select>
            <button type="submit" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 text-sm">搜索</button>
        </form>
        <a href="<?php echo e(route('admin.articles.create')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm whitespace-nowrap">
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
            <?php $__empty_1 = true; $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-500 font-mono"><?php echo e($article->id); ?></td>
                    <td class="px-6 py-4 max-w-xs">
                        <p class="text-sm font-medium text-gray-800 truncate"><?php echo e($article->title); ?></p>
                        <?php if($article->cover_image): ?>
                            <span class="text-xs text-gray-400">有封面图</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($article->user->name); ?></td>
                    <td class="px-6 py-4">
                        <?php $__currentLoopData = $article->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="text-xs px-2 py-0.5 rounded-full text-white" style="background-color: <?php echo e($tag->color); ?>"><?php echo e($tag->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($article->status === 'published'): ?>
                            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">已发布</span>
                        <?php else: ?>
                            <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-700">草稿</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500"><?php echo e($article->views_count); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-400">
                        <?php echo e($article->published_at ? $article->published_at->format('Y-m-d H:i') : '-'); ?>

                    </td>
                    <td class="px-6 py-4 text-right whitespace-nowrap">
                        <a href="<?php echo e(route('admin.articles.edit', $article)); ?>" class="text-blue-600 hover:text-blue-800 text-sm mr-2">编辑</a>
                        <?php if($article->status === 'published'): ?>
                            <a href="<?php echo e(route('article.show', $article->slug)); ?>" target="_blank" class="text-gray-500 hover:text-gray-700 text-sm mr-2">查看</a>
                        <?php endif; ?>
                        <form action="<?php echo e(route('admin.articles.destroy', $article)); ?>" method="POST" class="inline" onsubmit="return confirm('确定删除此文章？')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">删除</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-400">暂无文章，点击右上角开始写作</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-4">
    <?php echo e($articles->appends(request()->query())->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/ydxred.com/resources/views/admin/articles/index.blade.php ENDPATH**/ ?>