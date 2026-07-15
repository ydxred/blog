@extends('admin.layouts.app')

@section('content')
@php
    $fmt = fn ($b) => $b >= 1048576 ? number_format($b / 1048576, 1) . ' MB' : number_format(max($b, 0) / 1024, 0) . ' KB';
    $tabs = ['all' => '全部', 'unused' => '仅未使用', 'articles' => '文章图', 'posts' => '动态图', 'uploads' => '图床', 'avatars' => '头像'];
@endphp
<style>
    .mm-head { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; margin-bottom:.9rem; }
    .mm-head h2 { font-size:1.5rem; font-weight:700; color:#1f2937; margin:0; }
    .mm-stat { font-size:.875rem; color:#6b7280; }
    .mm-stat b { color:#374151; }
    .mm-stat .u { color:#dc2626; font-weight:600; }
    .mm-note { background:#fffbeb; border:1px solid #fde68a; color:#92400e; font-size:.78rem; line-height:1.6; border-radius:10px; padding:.55rem .9rem; margin-bottom:1.1rem; }
    .mm-bar { display:flex; align-items:center; flex-wrap:wrap; gap:.5rem; margin-bottom:1.1rem; }
    .mm-tab { padding:.38rem .85rem; border-radius:999px; font-size:.85rem; text-decoration:none; color:#4b5563; background:#fff; border:1px solid #e5e7eb; transition:.15s; }
    .mm-tab:hover { background:#f9fafb; }
    .mm-tab.on { background:#2563eb; color:#fff; border-color:#2563eb; box-shadow:0 1px 3px rgba(37,99,235,.3); }
    .mm-actions { margin-left:auto; display:flex; align-items:center; gap:1rem; }
    .mm-link { font-size:.85rem; color:#4b5563; background:none; border:none; cursor:pointer; padding:0; }
    .mm-link:hover { color:#111827; }
    .mm-del { padding:.45rem 1.1rem; border-radius:8px; background:#dc2626; color:#fff; font-size:.85rem; font-weight:600; border:none; cursor:pointer; transition:.15s; }
    .mm-del:hover:not(:disabled) { background:#b91c1c; }
    .mm-del:disabled { opacity:.4; cursor:not-allowed; }
    .mm-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(150px, 1fr)); gap:.9rem; }
    .mm-card { position:relative; display:block; border-radius:12px; overflow:hidden; border:1px solid #e5e7eb; background:#fff; cursor:pointer; transition:box-shadow .15s, border-color .15s, transform .15s; }
    .mm-card:hover { box-shadow:0 6px 16px rgba(0,0,0,.08); transform:translateY(-1px); }
    .mm-card.sel { border-color:#ef4444; box-shadow:0 0 0 2px #ef4444; }
    .mm-thumb { aspect-ratio:1/1; background:#f3f4f6; overflow:hidden; }
    .mm-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
    .mm-cb { position:absolute; top:.55rem; left:.55rem; z-index:2; width:1.15rem; height:1.15rem; accent-color:#dc2626; cursor:pointer;
             box-shadow:0 0 0 2px rgba(255,255,255,.9); border-radius:3px; }
    .mm-tag { position:absolute; top:.5rem; right:.5rem; background:#ef4444; color:#fff; font-size:.65rem; font-weight:600; padding:.12rem .45rem; border-radius:999px; box-shadow:0 1px 2px rgba(0,0,0,.2); }
    .mm-foot { display:flex; justify-content:space-between; align-items:center; padding:.4rem .65rem; font-size:.7rem; color:#9ca3af; }
    .mm-foot .d { text-transform:none; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .mm-empty { grid-column:1 / -1; text-align:center; color:#9ca3af; padding:4rem 0; }
</style>

<div x-data="mediaManager()">
    <div class="mm-head">
        <h2>图片管理</h2>
        <div class="mm-stat">
            共 <b>{{ $stats['total'] }}</b> 张 · 占用 {{ $fmt($stats['total_bytes']) }} ·
            <span class="u">未使用 {{ $stats['unused'] }} 张（{{ $fmt($stats['unused_bytes']) }}）</span>
        </div>
    </div>

    <div class="mm-note">
        <b>「未使用」</b>= 未在任何文章封面/正文、动态配图、头像或"关于我"里检测到引用。删除会连同 WebP 副本一起移除、<b>不可恢复</b>，请先看缩略图确认。
    </div>

    <div class="mm-bar">
        @foreach ($tabs as $k => $label)
            <a href="?filter={{ $k }}" class="mm-tab {{ $filter === $k ? 'on' : '' }}">{{ $label }}</a>
        @endforeach
        <div class="mm-actions">
            <button type="button" class="mm-link" @click="selectAllUnused()">选中本页未使用</button>
            <button type="button" class="mm-link" x-show="selected.length" @click="clearSel()" x-cloak>清空</button>
            <button type="button" class="mm-del" :disabled="!selected.length" @click="del()">
                删除选中<span x-show="selected.length" x-text="'（'+selected.length+'）'"></span>
            </button>
        </div>
    </div>

    <div class="mm-grid">
        @forelse ($images as $img)
            <label class="mm-card" :class="selected.includes(@js($img['path'])) ? 'sel' : ''">
                <input type="checkbox" class="mm-cb media-cb" value="{{ $img['path'] }}"
                       data-used="{{ $img['used'] ? 1 : 0 }}" @change="toggle(@js($img['path']), $event.target.checked)">
                <div class="mm-thumb"><img src="{{ $img['url'] }}" loading="lazy" alt=""></div>
                @unless ($img['used'])
                    <span class="mm-tag">未使用</span>
                @endunless
                <div class="mm-foot">
                    <span class="d">{{ $img['dir'] }}</span>
                    <span>{{ $fmt($img['size']) }}</span>
                </div>
            </label>
        @empty
            <p class="mm-empty">这个筛选下没有图片</p>
        @endforelse
    </div>

    @if ($images->hasPages())
        <div style="margin-top:1.5rem;">{{ $images->links() }}</div>
    @endif
</div>

<script>
function mediaManager() {
    return {
        selected: [],
        toggle(path, on) {
            if (on) { if (!this.selected.includes(path)) this.selected.push(path); }
            else { this.selected = this.selected.filter(p => p !== path); }
        },
        selectAllUnused() {
            document.querySelectorAll('.media-cb[data-used="0"]').forEach(cb => { cb.checked = true; this.toggle(cb.value, true); });
        },
        clearSel() {
            this.selected = [];
            document.querySelectorAll('.media-cb').forEach(cb => cb.checked = false);
        },
        async del() {
            if (!this.selected.length) return;
            let usedCount = 0;
            this.selected.forEach(p => {
                const cb = document.querySelector('.media-cb[value="' + p.replace(/"/g, '\\"') + '"]');
                if (cb && cb.dataset.used === '1') usedCount++;
            });
            let msg = '确定删除选中的 ' + this.selected.length + ' 张图片？此操作不可恢复。';
            if (usedCount) msg = '⚠️ 选中的 ' + this.selected.length + ' 张里有 ' + usedCount + ' 张【正在使用】！\n删除后相关文章/动态里的图会失效。仍要删除吗？';
            if (!confirm(msg)) return;
            try {
                const res = await fetch(@js(route('admin.media.destroy')), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json', 'Accept': 'application/json',
                    },
                    body: JSON.stringify({ paths: this.selected }),
                });
                if (res.ok) { location.reload(); } else { alert('删除失败'); }
            } catch (e) { alert('删除请求出错：' + e.message); }
        },
    };
}
</script>
@endsection
