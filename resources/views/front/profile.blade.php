@extends('front.layout')

@section('title', '个人资料')

@section('content')
<div class="max-w-2xl mx-auto">
    <header class="mb-8">
        <h1 class="text-2xl font-title font-bold text-slate-800">个人资料</h1>
        <p class="text-sm text-slate-500 mt-1">管理你的账号信息、密码和危险操作</p>
    </header>

    <div class="space-y-6">
        {{-- 基本信息 --}}
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data"
              class="rounded-2xl bg-white/90 backdrop-blur-sm p-6 sm:p-7 shadow-sm ring-1 ring-slate-200/80 space-y-5"
              x-data="avatarForm()">
            @csrf
            @method('PATCH')

            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-indigo-500" aria-hidden="true"></span>
                基本信息
            </h2>

            {{-- 头像 --}}
            <div class="flex items-center gap-4">
                <button type="button" class="relative group rounded-full" @click="$refs.avatarInput.click()" aria-label="更换头像">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="当前头像"
                             class="w-20 h-20 rounded-full object-cover ring-2 ring-indigo-100" x-ref="avatarPreview">
                    @else
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-2xl font-bold ring-2 ring-indigo-100"
                             x-ref="avatarPreview" x-show="!previewUrl">
                            {{ mb_substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    <img x-show="previewUrl" :src="previewUrl" class="w-20 h-20 rounded-full object-cover absolute inset-0" x-cloak alt="新头像预览">
                    <div class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition" aria-hidden="true">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <input type="file" name="avatar" accept="image/*" class="hidden" x-ref="avatarInput" @change="handleAvatar($event)">
                </button>
                <div>
                    <p class="text-sm font-medium text-slate-700">点击更换头像</p>
                    <p class="text-xs text-slate-400 mt-0.5">支持 JPG/PNG/GIF/HEIC，最大 20MB</p>
                </div>
            </div>

            <div>
                <label for="profile-name" class="block text-sm font-medium text-slate-700 mb-1.5">昵称</label>
                <input id="profile-name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                       autocomplete="nickname"
                       class="w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
            </div>

            <div>
                <label for="profile-email" class="block text-sm font-medium text-slate-700 mb-1.5">邮箱</label>
                <input id="profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                       autocomplete="email"
                       class="w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
            </div>

            <div>
                <label for="profile-bio" class="block text-sm font-medium text-slate-700 mb-1.5">个人简介</label>
                <textarea id="profile-bio" name="bio" rows="3" maxlength="500"
                          class="w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors resize-none"
                          placeholder="介绍一下自己...">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center rounded-full bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-2.5 text-sm font-medium text-white hover:from-indigo-500 hover:to-violet-500 shadow-sm shadow-indigo-500/20 transition-all">
                保存资料
            </button>
        </form>

        {{-- 修改密码 --}}
        <form action="{{ route('profile.password') }}" method="POST"
              class="rounded-2xl bg-white/90 backdrop-blur-sm p-6 sm:p-7 shadow-sm ring-1 ring-slate-200/80 space-y-5">
            @csrf
            @method('PUT')

            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-teal-500" aria-hidden="true"></span>
                修改密码
            </h2>

            <div>
                <label for="profile-current-password" class="block text-sm font-medium text-slate-700 mb-1.5">当前密码</label>
                <input id="profile-current-password" type="password" name="current_password" required
                       autocomplete="current-password"
                       class="w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
            </div>
            <div>
                <label for="profile-new-password" class="block text-sm font-medium text-slate-700 mb-1.5">新密码</label>
                <input id="profile-new-password" type="password" name="password" required
                       autocomplete="new-password"
                       class="w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
            </div>
            <div>
                <label for="profile-confirm-password" class="block text-sm font-medium text-slate-700 mb-1.5">确认新密码</label>
                <input id="profile-confirm-password" type="password" name="password_confirmation" required
                       autocomplete="new-password"
                       class="w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-colors">
            </div>

            <button type="submit" class="w-full inline-flex items-center justify-center rounded-full bg-slate-800 px-6 py-2.5 text-sm font-medium text-white hover:bg-slate-900 transition-colors">
                修改密码
            </button>
        </form>

        {{-- 删除账号 --}}
        <section class="rounded-2xl bg-white/90 backdrop-blur-sm p-6 sm:p-7 shadow-sm ring-1 ring-red-100" x-data="{ showDelete: false }" aria-labelledby="danger-zone-title">
            <h2 id="danger-zone-title" class="text-base font-bold text-red-600 flex items-center gap-2">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-red-500" aria-hidden="true"></span>
                危险操作
            </h2>
            <p class="text-sm text-slate-500 mt-1.5">删除账号后，所有数据将无法恢复。</p>
            <button type="button" @click="showDelete = !showDelete"
                    :aria-expanded="showDelete ? 'true' : 'false'"
                    aria-controls="delete-account-form"
                    class="mt-3 text-sm text-red-500 hover:text-red-700 font-medium">
                删除我的账号
            </button>

            <form id="delete-account-form" action="{{ route('profile.destroy') }}" method="POST" x-show="showDelete" x-cloak class="mt-4 space-y-3">
                @csrf
                @method('DELETE')
                <div>
                    <label for="profile-delete-password" class="block text-sm font-medium text-slate-700 mb-1.5">输入密码确认</label>
                    <input id="profile-delete-password" type="password" name="password" required
                           autocomplete="current-password"
                           class="w-full rounded-xl border-slate-200 bg-slate-50/50 text-sm focus:bg-white focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-colors">
                </div>
                <button type="submit" class="inline-flex items-center rounded-full bg-red-600 px-5 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors"
                        onclick="return confirm('确定要删除账号吗？此操作不可撤销！')">
                    确认删除账号
                </button>
            </form>
        </section>
    </div>
</div>

<script>
    function avatarForm() {
        return {
            previewUrl: null,
            handleAvatar(event) {
                const file = event.target.files[0];
                if (file) this.previewUrl = URL.createObjectURL(file);
            }
        }
    }
</script>
@endsection
