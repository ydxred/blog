<?php $__env->startSection('title', $article->title); ?>

<?php $__env->startSection('styles'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<style>
    /* TOC Styles */
    #toc ul { list-style: none; padding-left: 1rem; }
    #toc > ul { padding-left: 0; }
    #toc li { margin-bottom: 0.6rem; }
    #toc a { color: #64748b; text-decoration: none; display: block; transition: all 0.2s; line-height: 1.4; }
    #toc a:hover { color: #4f46e5; }
    #toc .is-active-link { color: #4f46e5; font-weight: 600; }
    #toc .is-collapsed { display: none; }
    /* Code Copy */
    .prose pre { position: relative; }
    .prose pre code { background: transparent; padding: 0; color: inherit; font-weight: normal; }
    .copy-btn { position: absolute; top: 0.5rem; right: 0.5rem; padding: 0.25rem 0.5rem; background: rgba(255,255,255,0.1); border-radius: 0.375rem; color: #e2e8f0; font-size: 0.75rem; cursor: pointer; transition: all 0.2s; border: 1px solid rgba(255,255,255,0.2); opacity: 0; }
    .prose pre:hover .copy-btn { opacity: 1; }
    .copy-btn:hover { background: rgba(255,255,255,0.2); }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-col lg:flex-row gap-8 max-w-5xl mx-auto items-start">
    <div class="flex-1 min-w-0 w-full">
        <div class="mb-6">
        <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center gap-1.5 text-sm font-medium text-stone-500 hover:text-indigo-600 transition-colors bg-white/50 backdrop-blur-sm px-3 py-1.5 rounded-full ring-1 ring-stone-200/80 hover:ring-indigo-300">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            返回列表
        </a>
    </div>

    <article class="rounded-2xl border border-slate-200/80 bg-white/90 backdrop-blur-sm p-6 sm:p-10 shadow-sm mb-10 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-400 to-indigo-500"></div>
        <header class="mb-8 mt-2">
            <?php if($article->tags->count()): ?>
                <div class="flex flex-wrap gap-2 mb-5">
                    <?php $__currentLoopData = $article->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('home', ['tag' => $tag->slug])); ?>"
                           class="text-xs px-3 py-1 rounded-full font-medium hover:opacity-80 transition-opacity text-white shadow-sm"
                           style="background: linear-gradient(135deg, <?php echo e($tag->color); ?>, <?php echo e($tag->color); ?>dd);">
                            <?php echo e($tag->name); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <h1 class="text-3xl sm:text-4xl font-title font-bold text-slate-800 leading-snug tracking-tight mb-6"><?php echo e($article->title); ?></h1>

            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-500">
                <span class="flex items-center gap-2">
                    <?php if($article->user->avatar): ?>
                        <img src="<?php echo e(asset('storage/' . $article->user->avatar)); ?>" alt="" class="w-8 h-8 rounded-full object-cover ring-2 ring-indigo-50">
                    <?php endif; ?>
                    <span class="font-medium text-slate-700"><?php echo e($article->user->name); ?></span>
                </span>
                <span aria-hidden="true">&bull;</span>
                <time datetime="<?php echo e($article->published_at->toIso8601String()); ?>"><?php echo e($article->published_at->format('Y年n月j日')); ?></time>
                <span aria-hidden="true">&bull;</span>
                <span><?php echo e($article->read_time); ?> 分钟阅读</span>
                <?php if($article->views_count > 0): ?>
                    <span aria-hidden="true">&bull;</span>
                    <span><?php echo e($article->views_count); ?> 次阅读</span>
                <?php endif; ?>
            </div>
        </header>

        <?php if($article->cover_image): ?>
            <div class="mb-10 rounded-xl overflow-hidden ring-1 ring-slate-200/60 shadow-sm">
                <img src="<?php echo e(asset('storage/' . $article->cover_image)); ?>" alt="" class="w-full object-cover max-h-[32rem]">
            </div>
        <?php endif; ?>

        <div class="prose prose-slate prose-lg max-w-none
                    prose-headings:font-title prose-headings:font-bold prose-headings:text-slate-800
                    prose-a:text-indigo-600 hover:prose-a:text-indigo-500
                    prose-code:text-violet-600 prose-code:bg-slate-100/80 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded-md prose-code:font-medium
                    prose-pre:bg-slate-900 prose-pre:text-slate-50 prose-pre:shadow-sm prose-pre:p-4
                    prose-img:rounded-xl prose-img:ring-1 prose-img:ring-slate-200/60
                    prose-blockquote:border-l-indigo-400 prose-blockquote:bg-indigo-50/50 prose-blockquote:py-1 prose-blockquote:pr-4 prose-blockquote:rounded-r-lg prose-blockquote:text-slate-600 prose-blockquote:not-italic">
            <?php echo \Illuminate\Support\Str::markdown($article->content); ?>

        </div>

        <div class="mt-12 pt-6 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <button type="button" class="like-btn inline-flex items-center gap-2 px-4 py-2 rounded-full ring-1 ring-slate-200 bg-white/50 text-slate-500 hover:text-pink-500 hover:ring-pink-200 transition-all font-medium text-sm" data-type="article" data-id="<?php echo e($article->id); ?>">
                    <svg class="w-5 h-5 like-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span class="like-count"><?php echo e($article->likes_count); ?></span>
                </button>
                <span class="text-sm font-medium text-slate-500">分享这篇文章</span>
            </div>
            <div class="social-share" data-sites="weibo,qq,wechat,twitter" data-mobile-sites="weibo,qq,wechat,twitter"
                 data-url="<?php echo e(route('article.show', $article->slug)); ?>" data-title="<?php echo e($article->title); ?>"
                 data-description="<?php echo e($article->excerpt ?: Str::limit(strip_tags($article->content), 120)); ?>">
            </div>
        </div>
    </article>

    <?php if($relatedArticles->count()): ?>
    <section class="mb-10">
        <h2 class="text-sm font-bold text-stone-400 uppercase tracking-widest mb-5 ml-1">Related / 相关阅读</h2>
        <div class="grid gap-4 sm:grid-cols-2">
            <?php $__currentLoopData = $relatedArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $related): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('article.show', $related->slug)); ?>" class="group flex gap-4 rounded-xl border border-stone-200/80 bg-white/60 p-3 hover:bg-white hover:border-indigo-200 hover:shadow-md hover:shadow-indigo-500/5 transition-all">
                    <?php if($related->cover_image): ?>
                        <div class="w-24 h-20 shrink-0 rounded-lg overflow-hidden bg-stone-100 ring-1 ring-stone-200/50">
                            <img src="<?php echo e(asset('storage/' . $related->cover_image)); ?>" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                    <?php endif; ?>
                    <div class="min-w-0 flex flex-col justify-center">
                        <p class="text-sm font-bold text-stone-800 line-clamp-2 group-hover:text-indigo-600 transition-colors leading-snug"><?php echo e($related->title); ?></p>
                        <p class="text-xs text-stone-400 mt-2 font-medium"><?php echo e($related->published_at->format('Y-m-d')); ?></p>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="rounded-2xl border border-stone-200/80 bg-white/90 backdrop-blur-sm p-6 sm:p-10 shadow-sm">
        <h2 class="text-xl font-title font-bold text-stone-800">评论 <span class="text-stone-400 font-normal text-base ml-1">(<?php echo e($article->approvedComments->count()); ?>)</span></h2>

        <div class="mt-8 space-y-5">
            <?php $__empty_1 = true; $__currentLoopData = $article->approvedComments->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $comment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex gap-4 pb-5 border-b border-stone-100 last:border-0 last:pb-0">
                    <div class="h-10 w-10 shrink-0 rounded-full bg-gradient-to-br from-indigo-100 to-violet-100 text-indigo-700 flex items-center justify-center text-sm font-bold ring-2 ring-white shadow-sm">
                        <?php echo e(mb_substr($comment->nickname, 0, 1)); ?>

                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-baseline gap-2 mb-1">
                            <span class="text-sm font-bold text-stone-800"><?php echo e($comment->nickname); ?></span>
                            <span class="text-xs font-medium text-stone-400"><?php echo e($comment->created_at->diffForHumans()); ?></span>
                        </div>
                        <p class="text-sm text-stone-600 leading-relaxed"><?php echo e($comment->content); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="py-8 text-center rounded-xl bg-stone-50 border border-dashed border-stone-200">
                    <p class="text-sm text-stone-500">还没有人评论，来抢个沙发吧</p>
                </div>
            <?php endif; ?>
        </div>

        <form action="<?php echo e(route('article.comment', $article)); ?>" method="POST" class="mt-8 pt-6 border-t border-stone-200/80 space-y-4">
            <?php echo csrf_field(); ?>
            <p class="text-sm font-bold text-stone-700">发表评论</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="nickname" placeholder="昵称 *" required value="<?php echo e(old('nickname')); ?>"
                       class="w-full rounded-xl border-stone-200 bg-stone-50/50 text-sm px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
                <input type="email" name="email" placeholder="邮箱（选填）" value="<?php echo e(old('email')); ?>"
                       class="w-full rounded-xl border-stone-200 bg-stone-50/50 text-sm px-4 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
            </div>
            <textarea name="content" rows="4" placeholder="写下你的想法..." required
                      class="w-full rounded-xl border-stone-200 bg-stone-50/50 text-sm px-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors resize-none"><?php echo e(old('content')); ?></textarea>
            <div class="text-right">
                <button type="submit" class="inline-flex items-center justify-center rounded-full bg-stone-800 px-6 py-2.5 text-sm font-medium text-white hover:bg-indigo-600 shadow-sm transition-colors">
                    提交评论
                </button>
            </div>
        </form>
    </section>
    </div>
    
    <aside class="hidden lg:block w-64 shrink-0 sticky top-20">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-5 shadow-sm ring-1 ring-slate-200/80">
            <h3 class="font-bold text-slate-800 mb-3 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                文章目录
            </h3>
            <div id="toc" class="text-sm"></div>
        </div>
    </aside>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tocbot/4.21.0/tocbot.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Fancybox
    Fancybox.bind('[data-fancybox="gallery"]', {
        groupAll: true,
    });
    // Wrap images in a tags dynamically for Fancybox
    document.querySelectorAll('.prose img').forEach(img => {
        const a = document.createElement('a');
        a.href = img.src;
        a.dataset.fancybox = 'gallery';
        img.parentNode.insertBefore(a, img);
        a.appendChild(img);
    });

    // 2. TOC
    const content = document.querySelector('.prose');
    if (content) {
        const headings = content.querySelectorAll('h2, h3, h4');
        if (headings.length > 0) {
            headings.forEach((heading, index) => {
                heading.id = heading.id || 'heading-' + index;
            });
            tocbot.init({
                tocSelector: '#toc',
                contentSelector: '.prose',
                headingSelector: 'h2, h3, h4',
                hasInnerContainers: true,
                collapseDepth: 6
            });
        } else {
            const aside = document.querySelector('aside');
            if(aside) aside.style.display = 'none';
        }
    }

    // 3. Code Copy
    document.querySelectorAll('.prose pre').forEach(pre => {
        const btn = document.createElement('button');
        btn.className = 'copy-btn';
        btn.textContent = '复制';
        pre.appendChild(btn);

        btn.addEventListener('click', () => {
            const code = pre.querySelector('code').innerText;
            navigator.clipboard.writeText(code).then(() => {
                btn.textContent = '已复制!';
                setTimeout(() => btn.textContent = '复制', 2000);
            });
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/ydxred.com/resources/views/front/blog/show.blade.php ENDPATH**/ ?>