@extends('admin.layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold text-gray-800">写文章</h2>
    <a href="{{ route('admin.articles.index') }}" class="text-gray-500 hover:text-gray-700 text-sm">&larr; 返回列表</a>
</div>

<div x-data="articleAutoSave()">
    {{-- 恢复提示 --}}
    <div x-show="hasBackup" x-cloak
         class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <div>
                <p class="text-sm font-medium text-amber-800">检测到未保存的草稿</p>
                <p class="text-xs text-amber-600 mt-0.5">上次编辑于 <span x-text="backupTime"></span></p>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="button" @click="restoreBackup()" class="px-3 py-1.5 bg-amber-500 text-white rounded-lg text-sm hover:bg-amber-600 transition">恢复草稿</button>
            <button type="button" @click="clearBackup()" class="px-3 py-1.5 bg-white text-gray-600 border border-gray-200 rounded-lg text-sm hover:bg-gray-50 transition">丢弃</button>
        </div>
    </div>

    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" @submit="clearBackup()">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="mb-4">
                        <input type="text" name="title" x-model="title" value="{{ old('title') }}" placeholder="文章标题"
                               class="w-full text-2xl font-bold border-0 border-b-2 border-gray-200 focus:border-blue-500 focus:ring-0 px-0 py-2 placeholder-gray-300"
                               required>
                        @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <textarea name="excerpt" rows="2" x-model="excerpt" placeholder="文章摘要（选填，用于列表展示）"
                                  class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('excerpt') }}</textarea>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">文章内容</label>
                            <span class="text-xs text-gray-400" x-show="lastSaveTime" x-text="'自动保存于 ' + lastSaveTime"></span>
                        </div>
                        <textarea name="content" id="editor" rows="20"
                                  class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">{{ old('content') }}</textarea>
                        @error('content')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">发布设置</h3>
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-1">状态</label>
                        <select name="status" class="w-full border-gray-200 rounded-lg text-sm">
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>草稿</option>
                            <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>立即发布</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        保存文章
                    </button>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">封面图片</h3>
                    <div x-data="{ preview: null }">
                        <input type="file" name="cover_image" accept="image/*" class="hidden" id="cover-input"
                               @change="const f = $event.target.files[0]; if(f) { const r = new FileReader(); r.onload = e => preview = e.target.result; r.readAsDataURL(f); }">
                        <div x-show="!preview" class="border-2 border-dashed border-gray-200 rounded-lg p-6 text-center cursor-pointer hover:border-blue-400 transition"
                             @click="document.getElementById('cover-input').click()">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-xs text-gray-400">点击上传封面</p>
                        </div>
                        <div x-show="preview" class="relative">
                            <img :src="preview" class="w-full rounded-lg">
                            <button type="button" @click="preview = null; document.getElementById('cover-input').value = ''"
                                    class="absolute top-1 right-1 w-6 h-6 bg-black/60 rounded-full text-white text-xs flex items-center justify-center">&times;</button>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">标签</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($tags as $tag)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="hidden peer tag-checkbox"
                                       data-tag-id="{{ $tag->id }}"
                                       {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                                <span class="inline-block px-3 py-1 rounded-full text-xs border-2 transition-all
                                             peer-checked:text-white peer-checked:border-transparent
                                             text-gray-600 border-gray-200 hover:border-gray-300"
                                      x-data
                                      x-init="if($el.previousElementSibling.checked) { $el.style.backgroundColor = '{{ $tag->color }}'; $el.style.color = 'white'; $el.style.borderColor = '{{ $tag->color }}'; }"
                                      @click="$nextTick(() => { const c = $el.previousElementSibling.checked; $el.style.backgroundColor = c ? '{{ $tag->color }}' : ''; $el.style.color = c ? 'white' : ''; $el.style.borderColor = c ? '{{ $tag->color }}' : ''; scheduleSave(); })">
                                    {{ $tag->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
<script>
function articleAutoSave() {
    const STORAGE_KEY = 'article_draft_create';
    return {
        title: '{{ old("title", "") }}',
        excerpt: '{{ old("excerpt", "") }}',
        hasBackup: false,
        backupTime: '',
        lastSaveTime: '',
        editor: null,
        saveTimer: null,

        init() {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    if (data.title || data.content || data.excerpt) {
                        this.hasBackup = true;
                        this.backupTime = data.savedAt || '未知时间';
                    }
                } catch(e) {}
            }

            this.$nextTick(() => {
                this.editor = new EasyMDE({
                    element: document.getElementById('editor'),
                    spellChecker: false,
                    placeholder: '开始写作...',
                    minHeight: '400px',
                    sideBySideFullscreen: false, // 分屏时不全屏
                    imageUpload: true, // 启用图片直接上传
                    imageAccept: 'image/png, image/jpeg, image/gif, image/webp', // 允许的图片格式
                    imageTexts: {
                        sbInit: '选择或拖拽图片上传',
                        sbOnDragEnter: '拖拽图片到这里上传',
                        sbOnDrop: '正在上传...',
                        sbProgress: '上传中... #progress#',
                        sbOnUploaded: '上传成功',
                        sizeUnits: ' B, KB, MB'
                    },
                    imageUploadFunction: (file, onSuccess, onError) => {
                        const formData = new FormData();
                        formData.append('image', file);
                        formData.append('_token', '{{ csrf_token() }}');

                        fetch('{{ route('admin.upload.image') }}', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                onSuccess(data.url);
                            } else {
                                onError(data.message || '上传失败');
                            }
                        })
                        .catch(err => {
                            onError('网络错误，上传失败');
                        });
                    },
                    toolbar: [
                        'bold', 'italic', 'heading', '|',
                        'quote', 'unordered-list', 'ordered-list', '|',
                        'link', 'upload-image', 'table', '|',
                        'code', 'horizontal-rule', '|',
                        'preview', 'side-by-side', 'fullscreen', '|',
                        'guide'
                    ],
                    status: ['lines', 'words'],
                });

                this.editor.codemirror.on('change', () => this.scheduleSave());

                this.$watch('title', () => this.scheduleSave());
                this.$watch('excerpt', () => this.scheduleSave());
            });
        },

        scheduleSave() {
            clearTimeout(this.saveTimer);
            this.saveTimer = setTimeout(() => this.saveToLocal(), 3000);
        },

        saveToLocal() {
            const content = this.editor ? this.editor.value() : '';
            if (!this.title && !content && !this.excerpt) return;

            const tags = [];
            document.querySelectorAll('.tag-checkbox:checked').forEach(cb => {
                tags.push(parseInt(cb.dataset.tagId));
            });

            const data = {
                title: this.title,
                excerpt: this.excerpt,
                content: content,
                tags: tags,
                savedAt: new Date().toLocaleString('zh-CN'),
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
            this.lastSaveTime = data.savedAt;
        },

        restoreBackup() {
            try {
                const data = JSON.parse(localStorage.getItem(STORAGE_KEY));
                if (!data) return;

                this.title = data.title || '';
                this.excerpt = data.excerpt || '';

                if (this.editor && data.content) {
                    this.editor.value(data.content);
                }

                if (data.tags && data.tags.length) {
                    document.querySelectorAll('.tag-checkbox').forEach(cb => {
                        const tagId = parseInt(cb.dataset.tagId);
                        cb.checked = data.tags.includes(tagId);
                        const span = cb.nextElementSibling;
                        if (cb.checked) {
                            span.style.backgroundColor = getComputedStyle(span).getPropertyValue('--tag-color') || span.getAttribute('data-color') || '';
                        }
                        span.click(); span.click();
                        if (data.tags.includes(tagId)) cb.checked = true;
                    });
                }

                this.hasBackup = false;
                this.lastSaveTime = data.savedAt;
            } catch(e) { console.error(e); }
        },

        clearBackup() {
            localStorage.removeItem(STORAGE_KEY);
            this.hasBackup = false;
        }
    };
}
</script>
@endsection
