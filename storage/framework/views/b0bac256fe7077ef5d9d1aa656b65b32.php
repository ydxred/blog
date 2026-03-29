<?php $__env->startSection('title', '关于我'); ?>

<?php $__env->startSection('hero'); ?>
<div class="relative overflow-hidden border-b border-stone-200/60 bg-white/40 backdrop-blur-sm">
    <div class="absolute inset-0 bg-gradient-to-b from-amber-50/50 to-transparent"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-5 py-12 sm:py-16 relative z-10">
        <h1 class="text-3xl sm:text-4xl font-title font-bold text-amber-900/90 tracking-tight">关于我</h1>
        <p class="mt-3 text-stone-500 sm:text-lg max-w-xl leading-relaxed">你好，欢迎来到我的角落。</p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">
    <article class="rounded-2xl bg-white/90 backdrop-blur-sm p-6 sm:p-10 shadow-sm ring-1 ring-slate-200/80 mb-10 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-orange-400 to-pink-500"></div>
        <div class="prose prose-slate prose-lg max-w-none
                    prose-headings:font-title prose-headings:font-bold prose-headings:text-slate-800
                    prose-a:text-pink-600 hover:prose-a:text-pink-500
                    prose-code:text-pink-700 prose-code:bg-slate-100/80 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded-md prose-code:font-medium
                    prose-pre:bg-slate-900 prose-pre:text-slate-50 prose-pre:shadow-sm
                    prose-img:rounded-xl prose-img:ring-1 prose-img:ring-slate-200/60
                    prose-blockquote:border-l-pink-400 prose-blockquote:bg-pink-50/50 prose-blockquote:py-1 prose-blockquote:pr-4 prose-blockquote:rounded-r-lg prose-blockquote:text-slate-600 prose-blockquote:not-italic mt-4">
            <?php echo \Illuminate\Support\Str::markdown($content); ?>

        </div>
    </article>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/ydxred.com/resources/views/front/about.blade.php ENDPATH**/ ?>