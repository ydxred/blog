<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['user', 'size' => 'w-10 h-10', 'text' => 'text-base']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['user', 'size' => 'w-10 h-10', 'text' => 'text-base']); ?>
<?php foreach (array_filter((['user', 'size' => 'w-10 h-10', 'text' => 'text-base']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<?php if($user->avatar): ?>
    <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt=""
         class="<?php echo e($size); ?> rounded-full object-cover flex-shrink-0 ring-2 ring-white">
<?php else: ?>
    <div class="<?php echo e($size); ?> rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold flex-shrink-0 <?php echo e($text); ?>">
        <?php echo e(mb_substr($user->name, 0, 1)); ?>

    </div>
<?php endif; ?>
<?php /**PATH /var/www/ydxred.com/resources/views/components/avatar.blade.php ENDPATH**/ ?>