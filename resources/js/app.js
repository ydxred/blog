import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// 延迟到 DOMContentLoaded 再启动：确保页面级脚本（如 moments.js 里的 postForm）
// 已注册完毕，再让 Alpine 求值 x-data，避免 “postForm is not defined” 导致组件失效。
if (document.readyState === 'complete') {
    Alpine.start();
} else {
    document.addEventListener('DOMContentLoaded', () => Alpine.start());
}
