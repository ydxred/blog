/* eslint-disable */
/**
 * 全站点赞按钮交互（仅前台 layout 引用）
 * 与服务端约定：
 *   - GET  /likes/status 返回 {posts:[...], articles:[...]}
 *   - POST /like/{type}/{id} 返回 {liked, likes_count}
 */
function initLikes() {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (!csrfMeta) return;
    const csrfToken = csrfMeta.content;

    // 1) 初始拉取本机已点赞状态
    fetch('/likes/status', { headers: { Accept: 'application/json' } })
        .then((r) => r.json())
        .then((data) => {
            document.querySelectorAll('.like-btn').forEach((btn) => {
                const type = btn.dataset.type;
                const id = parseInt(btn.dataset.id, 10);
                const list = data[type + 's'] || [];
                if (Object.values(list).includes(id)) {
                    markLiked(btn, true);
                }
            });
        })
        .catch(() => {});

    // 2) 委托点击
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.like-btn');
        if (!btn) return;
        e.preventDefault();
        if (btn.disabled) return;

        btn.disabled = true;
        const type = btn.dataset.type;
        const id = btn.dataset.id;
        const countEl = btn.querySelector('.like-count');

        fetch(`/like/${type}/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, Accept: 'application/json' },
        })
            .then((r) => r.json())
            .then((data) => {
                markLiked(btn, data.liked);
                if (countEl) {
                    countEl.textContent =
                        data.likes_count > 0
                            ? data.likes_count
                            : type === 'post' && !btn.classList.contains('px-4')
                              ? '喜欢'
                              : '0';
                }
            })
            .finally(() => {
                btn.disabled = false;
            });
    });
}

function markLiked(btn, liked) {
    const icon = btn.querySelector('.like-icon');
    btn.setAttribute('aria-pressed', liked ? 'true' : 'false');
    if (liked) {
        btn.classList.add('text-pink-500');
        btn.classList.remove('text-slate-400', 'text-slate-500');
        if (btn.classList.contains('ring-1')) btn.classList.add('ring-pink-200');
        icon && icon.classList.add('fill-current');
    } else {
        btn.classList.remove('text-pink-500');
        if (btn.classList.contains('ring-1')) btn.classList.remove('ring-pink-200');
        icon && icon.classList.remove('fill-current');
        const fallback = btn.dataset.type === 'article' ? 'text-slate-500' : 'text-slate-400';
        btn.classList.add(fallback);
    }
}

/** 回到顶部按钮 */
function initBackToTop() {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;
    const toggle = () => {
        const show = window.scrollY > 320;
        btn.classList.toggle('opacity-0', !show);
        btn.classList.toggle('pointer-events-none', !show);
        btn.classList.toggle('translate-y-2', !show);
    };
    window.addEventListener('scroll', toggle, { passive: true });
    toggle();
    btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
}

document.addEventListener('DOMContentLoaded', () => {
    initLikes();
    initBackToTop();
});
