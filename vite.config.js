import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // 全站
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/likes.js',
                // 按需
                'resources/js/article.js',      // 文章详情
                'resources/js/moments.js',      // 动态列表/详情
                'resources/js/share.js',        // 社会化分享
                'resources/js/admin-editor.js', // 后台编辑器
                'resources/js/admin-chart.js',  // 后台 dashboard
            ],
            refresh: true,
        }),
    ],
    build: {
        // 单独拆出大依赖，便于浏览器缓存
        rollupOptions: {
            output: {
                manualChunks: {
                    hljs: ['highlight.js/lib/common'],
                    fancybox: ['@fancyapps/ui'],
                    chart: ['chart.js/auto'],
                    easymde: ['easymde'],
                },
            },
        },
    },
});
