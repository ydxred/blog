/**
 * 动态列表页 + 动态详情页：Fancybox 图片预览
 * emoji-picker、加载更多、发帖表单 Alpine 组件分开导出
 */
import { Fancybox } from '@fancyapps/ui';
import '@fancyapps/ui/dist/fancybox/fancybox.css';

document.addEventListener('DOMContentLoaded', () => {
    Fancybox.bind('[data-fancybox]', { groupAll: true });
});

/** Alpine 发帖表单（在 layout 加载 Alpine 之前注册，所以用全局函数） */
window.postForm = function () {
    return {
        content: '',
        showTags: false,
        showEmoji: false,
        pickerReady: false,
        previews: [],
        files: [],
        async ensureEmojiLoaded() {
            // 按需懒加载 emoji-picker-element，省去首屏 200KB
            if (window.__emojiLoaded) return;
            window.__emojiLoaded = import('emoji-picker-element');
            await window.__emojiLoaded;
        },
        handleImages(event) {
            const newFiles = Array.from(event.target.files);
            for (let i = 0; i < newFiles.length && this.files.length < 15; i++) {
                const file = newFiles[i];
                const idx = this.files.length; // 先占位，保证 previews 与 files 索引严格对齐（避免异步 onload 乱序删错图）
                this.files.push(file);
                this.previews.push('');
                const reader = new FileReader();
                reader.onload = (e) => this.previews.splice(idx, 1, e.target.result);
                reader.readAsDataURL(file);
            }
            this.syncFileInput();
            event.target.value = '';
        },
        removeImage(index) {
            this.previews.splice(index, 1);
            this.files.splice(index, 1);
            this.syncFileInput();
        },
        syncFileInput() {
            const dt = new DataTransfer();
            this.files.forEach((f) => dt.items.add(f));
            this.$refs.fileInput.files = dt.files;
        },
        async toggleEmoji() {
            this.showEmoji = !this.showEmoji;
            this.showTags = false;
            if (this.showEmoji) {
                await this.ensureEmojiLoaded();
                this.initPicker();
            }
        },
        initPicker() {
            if (this.pickerReady) return;
            this.$nextTick(() => {
                const picker = this.$refs.emojiPanel.querySelector('emoji-picker');
                if (picker) {
                    picker.addEventListener('emoji-click', (e) => {
                        const emoji = e.detail.unicode;
                        const ta = this.$refs.textarea;
                        const start = ta.selectionStart;
                        const end = ta.selectionEnd;
                        this.content =
                            this.content.substring(0, start) + emoji + this.content.substring(end);
                        this.$nextTick(() => {
                            const pos = start + emoji.length;
                            ta.focus();
                            ta.setSelectionRange(pos, pos);
                        });
                    });
                    this.pickerReady = true;
                }
            });
        },
    };
};

/** 加载更多按钮 */
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('load-more-btn');
    if (!btn) return;

    const container = document.getElementById('posts-container');
    const shownCount = document.getElementById('shown-count');
    const textEl = document.getElementById('load-more-text');
    const spinner = document.getElementById('load-more-spinner');

    btn.addEventListener('click', () => {
        const url = btn.dataset.nextUrl;
        if (!url) return;

        btn.disabled = true;
        textEl.textContent = '加载中…';
        spinner.classList.remove('hidden');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then((r) => r.json())
            .then((data) => {
                const temp = document.createElement('div');
                temp.innerHTML = data.html;
                while (temp.firstElementChild) {
                    container.appendChild(temp.firstElementChild);
                }
                if (window.applyLikeState) window.applyLikeState(container);
                if (shownCount) {
                    shownCount.textContent = container.querySelectorAll(':scope > article').length;
                }
                if (data.next_page_url) {
                    btn.dataset.nextUrl = data.next_page_url;
                    btn.disabled = false;
                    textEl.textContent = '加载更多';
                    spinner.classList.add('hidden');
                } else {
                    btn.remove();
                }
            })
            .catch(() => {
                btn.disabled = false;
                textEl.textContent = '加载失败，重试';
                spinner.classList.add('hidden');
            });
    });
});
