<?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <article class="relative flex gap-4 sm:gap-5">
        <div class="flex-shrink-0 z-10 relative">
            <div class="ring-4 ring-[#f8fafc] rounded-full bg-white">
                <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['user' => $post->user]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($post->user)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $attributes = $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $component = $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
            </div>
        </div>
        <div class="flex-1 min-w-0 bg-white/80 backdrop-blur-sm rounded-2xl p-5 sm:p-6 shadow-sm ring-1 ring-slate-200/80 transition-shadow hover:shadow-md hover:shadow-teal-500/10 group relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-emerald-400 to-teal-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-slate-800"><?php echo e($post->user->name); ?></span>
                    <span class="text-slate-400 text-xs hidden sm:inline">&bull;</span>
                    <a href="<?php echo e(route('post.show', $post)); ?>" class="text-slate-400 hover:text-teal-600 text-xs transition-colors"><?php echo e($post->created_at->diffForHumans()); ?></a>
                </div>
            </div>

            <a href="<?php echo e(route('post.show', $post)); ?>" class="block mb-4">
                <p class="text-slate-700 text-[15px] leading-relaxed whitespace-pre-line group-hover:text-slate-900 transition-colors"><?php echo e($post->content); ?></p>
            </a>

            <?php if($post->images && count($post->images) > 0): ?>
                <div class="mb-4 grid <?php echo e(count($post->images) === 1 ? 'grid-cols-1 max-w-md' : (count($post->images) <= 4 ? 'grid-cols-2' : 'grid-cols-3')); ?> gap-2.5">
                    <?php $__currentLoopData = $post->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(asset('storage/' . $image)); ?>" data-fancybox="post-<?php echo e($post->id); ?>" class="rounded-xl overflow-hidden ring-1 ring-slate-200/60 block <?php echo e(count($post->images) === 1 ? '' : 'aspect-square'); ?>">
                            <img src="<?php echo e(asset('storage/' . $image)); ?>" alt=""
                                 class="w-full h-full <?php echo e(count($post->images) === 1 ? '' : 'object-cover'); ?> cursor-pointer hover:scale-105 transition-transform duration-300"
                                 loading="lazy">
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <?php if($post->tags->count()): ?>
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php $__currentLoopData = $post->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('moments', ['tag' => $tag->slug])); ?>"
                           class="text-xs px-2.5 py-1 rounded-md font-medium hover:opacity-80 transition-opacity text-white shadow-sm"
                           style="background: linear-gradient(135deg, <?php echo e($tag->color); ?>, <?php echo e($tag->color); ?>dd);">
                            #<?php echo e($tag->name); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <div class="flex flex-wrap items-center gap-5 pt-4 border-t border-slate-100">
                <button type="button" class="like-btn flex items-center gap-1.5 text-slate-400 hover:text-pink-500 transition-colors text-sm font-medium" data-type="post" data-id="<?php echo e($post->id); ?>">
                    <svg class="w-4 h-4 like-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span class="like-count"><?php echo e($post->likes_count > 0 ? $post->likes_count : '喜欢'); ?></span>
                </button>
                <a href="<?php echo e(route('post.show', $post)); ?>" class="flex items-center gap-1.5 text-slate-400 hover:text-teal-600 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <?php echo e($post->approvedComments->count() > 0 ? $post->approvedComments->count() : '评论'); ?>

                </a>
                <div class="social-share flex items-center opacity-70 hover:opacity-100 transition-opacity" data-sites="weibo,qq,wechat,twitter" data-size="small" data-disabled="google,facebook"
                     data-url="<?php echo e(route('post.show', $post)); ?>" data-title="<?php echo e(Str::limit($post->content, 60)); ?>" data-initialize="false">
                </div>
                <?php if(auth()->guard()->check()): ?>
                    <?php if($post->user_id === auth()->id() || auth()->user()->isAdmin()): ?>
                        <div class="ml-auto">
                            <form action="<?php echo e(route('post.destroy', $post)); ?>" method="POST" class="inline" onsubmit="return confirm('确定删除这条动态？')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-stone-300 hover:text-red-500 transition-colors text-sm">
                                    删除
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </article>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH /var/www/ydxred.com/resources/views/front/partials/post-list.blade.php ENDPATH**/ ?>