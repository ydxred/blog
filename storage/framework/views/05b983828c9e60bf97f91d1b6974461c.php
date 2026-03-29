<?php $__env->startSection('content'); ?>
<div class="max-w-lg mx-auto space-y-6">
    <h2 class="text-xl font-bold text-gray-800">个人资料</h2>

    
    <form action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5" x-data="avatarForm()">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PATCH'); ?>

        
        <div class="flex items-center gap-4">
            <div class="relative group cursor-pointer" @click="$refs.avatarInput.click()">
                <?php if($user->avatar): ?>
                    <img src="<?php echo e(asset('storage/' . $user->avatar)); ?>" alt=""
                         class="w-20 h-20 rounded-full object-cover" x-ref="avatarPreview">
                <?php else: ?>
                    <div class="w-20 h-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl font-bold"
                         x-ref="avatarPreview" x-show="!previewUrl">
                        <?php echo e(mb_substr($user->name, 0, 1)); ?>

                    </div>
                <?php endif; ?>
                <img x-show="previewUrl" :src="previewUrl" class="w-20 h-20 rounded-full object-cover absolute inset-0" x-cloak>
                <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <input type="file" name="avatar" accept="image/*" class="hidden" x-ref="avatarInput" @change="handleAvatar($event)">
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">点击更换头像</p>
                <p class="text-xs text-gray-400">支持 JPG/PNG/GIF/HEIC，最大 20MB</p>
            </div>
        </div>

        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">昵称</label>
            <input type="text" name="name" value="<?php echo e(old('name', $user->name)); ?>" required
                   class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">邮箱</label>
            <input type="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required
                   class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">个人简介</label>
            <textarea name="bio" rows="3" maxlength="500"
                      class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="介绍一下自己..."><?php echo e(old('bio', $user->bio)); ?></textarea>
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-blue-600 transition">
            保存资料
        </button>
    </form>

    
    <form action="<?php echo e(route('profile.password')); ?>" method="POST"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <h3 class="text-base font-semibold text-gray-800">修改密码</h3>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">当前密码</label>
            <input type="password" name="current_password" required
                   class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">新密码</label>
            <input type="password" name="password" required
                   class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">确认新密码</label>
            <input type="password" name="password_confirmation" required
                   class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <button type="submit" class="w-full bg-gray-800 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-gray-900 transition">
            修改密码
        </button>
    </form>

    
    <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6" x-data="{ showDelete: false }">
        <h3 class="text-base font-semibold text-red-600">危险操作</h3>
        <p class="text-sm text-gray-500 mt-1">删除账号后，所有数据将无法恢复。</p>
        <button type="button" @click="showDelete = !showDelete"
                class="mt-3 text-sm text-red-500 hover:text-red-700 font-medium">
            删除我的账号
        </button>

        <form action="<?php echo e(route('profile.destroy')); ?>" method="POST" x-show="showDelete" x-cloak class="mt-4 space-y-3">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">输入密码确认</label>
                <input type="password" name="password" required
                       class="w-full border-gray-200 rounded-lg text-sm focus:ring-red-500 focus:border-red-500">
            </div>
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition"
                    onclick="return confirm('确定要删除账号吗？此操作不可撤销！')">
                确认删除账号
            </button>
        </form>
    </div>
</div>

<script>
    function avatarForm() {
        return {
            previewUrl: null,
            handleAvatar(event) {
                const file = event.target.files[0];
                if (file) {
                    this.previewUrl = URL.createObjectURL(file);
                }
            }
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('front.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/ydxred.com/resources/views/front/profile.blade.php ENDPATH**/ ?>