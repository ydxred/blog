<?php $__env->startSection('hero'); ?>
<div class="relative overflow-hidden border-b border-slate-200/60 bg-white/40 backdrop-blur-sm">
    <div class="absolute inset-0 bg-gradient-to-b from-blue-50/50 to-transparent"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-5 py-12 sm:py-16 relative z-10">
        <h1 class="text-3xl sm:text-4xl font-title font-bold text-blue-900/90 tracking-tight">长文与笔记</h1>
        <p class="mt-3 text-slate-500 sm:text-lg max-w-xl leading-relaxed">这里沉淀了深入的技术探讨、生活感悟以及一些值得长久保留的思考。</p>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto">
    <?php if($tags->count()): ?>
    <div class="flex flex-wrap gap-2.5 mb-10">
        <a href="<?php echo e(route('home')); ?>"
           class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium transition-all <?php echo e(!$currentTag ? 'bg-gradient-to-r from-blue-500 to-indigo-500 text-white shadow-md shadow-blue-500/20' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-slate-300 hover:bg-slate-50'); ?>">
            全部
        </a>
        <?php $__currentLoopData = $tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('home', ['tag' => $tag->slug])); ?>"
               class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium transition-all <?php echo e($currentTag === $tag->slug ? 'text-white shadow-md' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-slate-300 hover:bg-slate-50'); ?>"
               <?php if($currentTag === $tag->slug): ?> style="background: linear-gradient(135deg, <?php echo e($tag->color); ?>, <?php echo e($tag->color); ?>dd); box-shadow: 0 4px 14px -2px <?php echo e($tag->color); ?>66;" <?php endif; ?>>
                <?php echo e($tag->name); ?>

            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <div class="space-y-6">
        <?php $__empty_1 = true; $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="group relative rounded-2xl bg-white/80 backdrop-blur-sm p-6 sm:p-8 shadow-sm ring-1 ring-slate-200/80 transition-all hover:shadow-lg hover:shadow-blue-500/10 hover:-translate-y-0.5 overflow-hidden">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-blue-400 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <a href="<?php echo e(route('article.show', $article->slug)); ?>" class="block">
                    <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-500 mb-3 font-medium">
                        <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded"><?php echo e($article->user->name); ?></span>
                        <span aria-hidden="true" class="self-center">&bull;</span>
                        <time datetime="<?php echo e($article->published_at->toIso8601String()); ?>" class="self-center"><?php echo e($article->published_at->format('Y年n月j日')); ?></time>
                        <span aria-hidden="true" class="self-center">&bull;</span>
                        <span class="self-center"><?php echo e($article->read_time); ?> 分钟阅读</span>
                        <?php if($article->views_count > 0): ?>
                            <span aria-hidden="true" class="self-center">&bull;</span>
                            <span class="self-center"><?php echo e($article->views_count); ?> 次阅读</span>
                        <?php endif; ?>
                        <?php if($article->approved_comments_count > 0): ?>
                            <span aria-hidden="true" class="self-center">&bull;</span>
                            <span class="self-center"><?php echo e($article->approved_comments_count); ?> 评论</span>
                        <?php endif; ?>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-title font-bold text-slate-800 group-hover:text-blue-600 transition-colors mb-3 leading-snug"><?php echo e($article->title); ?></h2>
                    <p class="text-slate-600 leading-relaxed line-clamp-2">
                        <?php echo e($article->excerpt ?: Str::limit(strip_tags($article->content), 180)); ?>

                    </p>
                    <?php if($article->tags->count()): ?>
                        <div class="flex flex-wrap gap-2 mt-4">
                            <?php $__currentLoopData = $article->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="text-xs px-2.5 py-1 rounded-md font-medium"
                                      style="color: <?php echo e($tag->color); ?>; background-color: <?php echo e($tag->color); ?>14;">
                                    <?php echo e($tag->name); ?>

                                </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </a>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white/50 backdrop-blur-sm py-20 text-center">
                <p class="text-slate-500 text-sm">这里目前还是空空如也</p>
                <p class="text-slate-400 text-xs mt-2"><?php if($currentTag): ?> 当前标签下没有内容 <?php else: ?> 敬请期待未来更新 <?php endif; ?></p>
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-10">
        <?php echo e($articles->appends(request()->query())->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/ydxred.com/resources/views/front/blog/index.blade.php ENDPATH**/ ?>