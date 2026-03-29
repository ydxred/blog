<?php $__env->startSection('content'); ?>
<div class="max-w-xl">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">新建标签</h2>

    <form action="<?php echo e(route('admin.tags.store')); ?>" method="POST" class="bg-white rounded-xl shadow-sm p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">标签名称</label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">颜色</label>
            <div class="flex items-center gap-3">
                <input type="color" name="color" value="<?php echo e(old('color', '#3b82f6')); ?>"
                       class="w-12 h-10 border-gray-300 rounded cursor-pointer">
                <span class="text-sm text-gray-500">选择标签颜色</span>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">排序（数字越小越靠前）</label>
            <input type="number" name="sort_order" value="<?php echo e(old('sort_order', 0)); ?>" min="0"
                   class="w-32 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">创建</button>
            <a href="<?php echo e(route('admin.tags.index')); ?>" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">取消</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/ydxred.com/resources/views/admin/tags/create.blade.php ENDPATH**/ ?>