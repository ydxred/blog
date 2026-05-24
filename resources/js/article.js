/**
 * 文章详情页：highlight.js + tocbot + fancybox + 代码块复制按钮
 */
import { Fancybox } from '@fancyapps/ui';
import '@fancyapps/ui/dist/fancybox/fancybox.css';

import hljs from 'highlight.js/lib/common';
import 'highlight.js/styles/atom-one-dark.css';

import tocbot from 'tocbot';

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

    // 3) 代码高亮
    content.querySelectorAll('pre code').forEach((block) => {
        try {
            hljs.highlightElement(block);
        } catch (_) {}
    });

    // 4) 代码块：语言标签 + 复制按钮
    content.querySelectorAll('pre').forEach((pre) => {
        const code = pre.querySelector('code');
        if (code && code.className) {
            const m = code.className.match(/language-(\w+)/);
            if (m) {
                const tag = document.createElement('span');
                tag.className = 'code-lang-tag';
                tag.textContent = m[1];
                pre.appendChild(tag);
            }
        }
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'copy-btn';
        btn.textContent = '复制';
        btn.setAttribute('aria-label', '复制代码');
        pre.appendChild(btn);
        btn.addEventListener('click', () => {
            const text = code ? code.innerText : pre.innerText;
            navigator.clipboard.writeText(text).then(() => {
                btn.textContent = '已复制!';
                setTimeout(() => (btn.textContent = '复制'), 2000);
            });
        });
    });
});
