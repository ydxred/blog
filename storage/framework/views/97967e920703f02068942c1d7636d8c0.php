<?php if($paginator->hasPages()): ?>
    <nav role="navigation" aria-label="分页导航" class="flex flex-col items-center gap-4">
        <div class="inline-flex items-center gap-1.5 rounded-full border border-stone-200/80 bg-white/80 backdrop-blur-sm p-1.5 shadow-sm">
            <?php if($paginator->onFirstPage()): ?>
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            <?php else: ?>
                <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-600 hover:bg-stone-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            <?php endif; ?>

            <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(is_string($element)): ?>
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-stone-400 font-medium"><?php echo e($element); ?></span>
                <?php endif; ?>

                <?php if(is_array($element)): ?>
                    <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $paginator->currentPage()): ?>
                            <span class="inline-flex items-center justify-center min-w-[2.25rem] h-9 px-2 rounded-full text-sm font-bold bg-stone-800 text-white shadow-sm"><?php echo e($page); ?></span>
                        <?php else: ?>
                            <a href="<?php echo e($url); ?>"
                               class="inline-flex items-center justify-center min-w-[2.25rem] h-9 px-2 rounded-full text-sm font-medium text-stone-600 hover:bg-stone-100 hover:text-stone-900 transition-colors"><?php echo e($page); ?></a>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($paginator->hasMorePages()): ?>
                <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-600 hover:bg-stone-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            <?php else: ?>
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-300 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            <?php endif; ?>
        </div>

        <p class="text-xs font-medium text-stone-400">
            第 <?php echo e($paginator->currentPage()); ?> / <?php echo e($paginator->lastPage()); ?> 页
        </p>
    </nav>
<?php endif; ?>
<?php /**PATH /var/www/ydxred.com/resources/views/vendor/pagination/tailwind.blade.php ENDPATH**/ ?>