<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

/**
 * 后台图片管理：列出 storage/app/public 下的内容图，标注"是否被引用"，
 * 方便清理未使用的孤儿图片。删除会连同 .webp 副本一并移除。
 */
class MediaController extends Controller
{
    /** 纳入管理的目录（头像也含，删当前头像会被"使用中"挡着提醒） */
    protected array $dirs = ['articles', 'posts', 'uploads', 'avatars'];

    public function index(Request $request)
    {
        $disk = Storage::disk('public');
        $haystack = $this->usedHaystack();

        $all = collect();
        foreach ($this->dirs as $d) {
            if (! $disk->exists($d)) {
                continue;
            }
            foreach ($disk->allFiles($d) as $f) {
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                    continue;
                }
                // 跳过某原图的 .webp sidecar（它跟随原图一起删，不单独列）
                if (str_ends_with(strtolower($f), '.webp') && $disk->exists(substr($f, 0, -5))) {
                    continue;
                }
                $all->push([
                    'path'     => $f,
                    'dir'      => explode('/', $f)[0],
                    'url'      => $disk->url($f),
                    'size'     => $disk->size($f),
                    'mtime'    => $disk->lastModified($f),
                    'used'     => str_contains($haystack, basename($f)),
                    'has_webp' => $disk->exists($f . '.webp'),
                ]);
            }
        }

        $stats = [
            'total'        => $all->count(),
            'total_bytes'  => $all->sum('size'),
            'unused'       => $all->where('used', false)->count(),
            'unused_bytes' => $all->where('used', false)->sum('size'),
        ];

        // 过滤
        $filter = (string) $request->query('filter', 'all');
        $items = $all;
        if ($filter === 'unused') {
            $items = $items->where('used', false);
        } elseif (in_array($filter, $this->dirs, true)) {
            $items = $items->where('dir', $filter);
        }

        // 排序：未使用优先，再按修改时间倒序（清理目标排最前）
        $items = $items->sort(fn ($a, $b) => [$a['used'], $b['mtime']] <=> [$b['used'], $a['mtime']])->values();

        // 手动分页
        $perPage = 48;
        $page = max(1, (int) $request->query('page', 1));
        $images = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.media.index', compact('images', 'stats', 'filter') + ['dirs' => $this->dirs]);
    }

    public function destroy(Request $request)
    {
        $paths = (array) $request->input('paths', []);
        $disk = Storage::disk('public');
        $deleted = 0;
        $freed = 0;

        foreach ($paths as $p) {
            if (! is_string($p)) {
                continue;
            }
            // 安全校验：仅限受管目录、禁止路径穿越
            if (str_contains($p, '..') || ! preg_match('#^(articles|posts|uploads|avatars)/#', $p)) {
                continue;
            }
            if ($disk->exists($p)) {
                $freed += $disk->size($p);
                $disk->delete($p);
                $disk->delete($p . '.webp'); // 连带 webp sidecar
                $deleted++;
            }
        }

        return response()->json(['deleted' => $deleted, 'freed' => $freed]);
    }

    /**
     * 拼一份"被引用内容"的大字符串：所有文章正文 + 封面路径 + 头像 + 动态配图 URL + 关于我。
     * 判定某图是否在用 = 它的文件名(随机20位，唯一)是否出现在其中。
     */
    protected function usedHaystack(): string
    {
        return implode("\n", [
            Article::withTrashed()->pluck('content')->implode("\n"),
            Article::withTrashed()->whereNotNull('cover_image')->pluck('cover_image')->implode("\n"),
            User::whereNotNull('avatar')->pluck('avatar')->implode("\n"),
            Post::withTrashed()->pluck('images')->filter()
                ->flatMap(fn ($a) => is_array($a) ? $a : [])->implode("\n"),
            (string) Setting::get('about_content', ''),
        ]);
    }
}
