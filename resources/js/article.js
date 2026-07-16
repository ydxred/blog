/**
 * 文章详情页：highlight.js + tocbot + fancybox + 代码块（CSDN/简书 风格顶部栏 + 复制）
 */
import { Fancybox } from '@fancyapps/ui';
import '@fancyapps/ui/dist/fancybox/fancybox.css';

import hljs from 'highlight.js/lib/common';
import 'highlight.js/styles/atom-one-dark.css';
import '../css/article-code.css';

import tocbot from 'tocbot';

const COPY_ICON =
    '<svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>';

document.addEventListener('DOMContentLoaded', () => {
    const content = document.querySelector('.prose');
    if (!content) return;

    // 1) Fancybox：把 .prose img 全包成 fancybox 链接
    content.querySelectorAll('img').forEach((img) => {
        if (img.closest('a')) return;
        const a = document.createElement('a');
        a.href = img.src;
        a.dataset.fancybox = 'gallery';
        img.parentNode.insertBefore(a, img);
        a.appendChild(img);
    });
    Fancybox.bind('[data-fancybox="gallery"]', { groupAll: true });

    // 2) TOC（如果没有 h2/h3/h4 就隐藏侧栏）
    const headings = content.querySelectorAll('h2, h3, h4');
    if (headings.length > 0) {
        headings.forEach((h, i) => {
            h.id = h.id || 'heading-' + i;
        });
        tocbot.init({
            tocSelector: '#toc',
            contentSelector: '.prose',
            headingSelector: 'h2, h3, h4',
            hasInnerContainers: true,
            collapseDepth: 6,
        });
    } else {
        document.querySelectorAll('aside.article-toc').forEach((a) => (a.style.display = 'none'));
    }

    // 3) 代码块：高亮 + 顶部栏（mac 三色点 + 语言 + 复制）
    content.querySelectorAll('pre').forEach((pre) => {
        const code = pre.querySelector('code');
        if (!code) return;

        // 语言：高亮前先取，避免被 hljs 改写
        const m = (code.className || '').match(/language-([\w-]+)/);
        const lang = m ? m[1] : 'text';

        try {
            hljs.highlightElement(code);
        } catch (_) {}

        // 包一层容器 + 顶部栏
        const wrap = document.createElement('div');
        wrap.className = 'cb';
        pre.parentNode.insertBefore(wrap, pre);

        const head = document.createElement('div');
        head.className = 'cb-head';
        head.innerHTML =
            '<span class="cb-dots"><i></i><i></i><i></i></span>' +
            '<span class="cb-lang"></span>' +
            '<button type="button" class="cb-copy" aria-label="复制代码">' + COPY_ICON + '<span>复制</span></button>';
        head.querySelector('.cb-lang').textContent = lang;
        wrap.appendChild(head);
        wrap.appendChild(pre);

        const btn = head.querySelector('.cb-copy');
        const label = btn.querySelector('span');
        btn.addEventListener('click', () => {
            navigator.clipboard.writeText(code.innerText).then(() => {
                btn.classList.add('done');
                label.textContent = '已复制';
                setTimeout(() => {
                    btn.classList.remove('done');
                    label.textContent = '复制';
                }, 2000);
            });
        });
    });
});
