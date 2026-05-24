<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Overtrue\Pinyin\Pinyin;

/**
 * 文章发布/更新服务
 *
 * 同时为后台 ArticleController 与 API PublishController 复用。
 * 接受统一的 payload 数组，自动处理：
 *   - tags 名称/ID 混合
 *   - cover_image 支持 文件 / URL / Base64
 *   - content 中外链图自动下载落地
 *   - excerpt 自动截取
 *   - slug 中文自动拼音
 */
class ArticlePublisher
{
    /**
     * 创建文章。
     *
     * @param  array{
     *   title:string,
     *   content:string,
     *   excerpt?:?string,
     *   slug?:?string,
     *   status?:string,
     *   published_at?:string|null,
     *   cover_image?:UploadedFile|string|null,
     *   cover_image_url?:string|null,
     *   cover_image_base64?:string|null,
     *   tags?:array|null,
     *   user_id?:int|null,
     *   fetch_remote_images?:bool,
     * } $payload
     */
    public function create(array $payload): Article
    {
        $title = $payload['title'];
        $status = $payload['status'] ?? 'draft';

        $slug = $this->resolveSlug($payload['slug'] ?? null, $title);
        $coverPath = $this->resolveCoverImage($payload);
        $content = $payload['content'];
        if ($payload['fetch_remote_images'] ?? true) {
            $content = $this->fetchRemoteImages($content);
        }
        $excerpt = $this->resolveExcerpt($payload['excerpt'] ?? null, $content);
        $publishedAt = $this->resolvePublishedAt($payload['published_at'] ?? null, $status);

        $article = Article::create([
            'user_id'      => $payload['user_id'] ?? auth()->id(),
            'title'        => $title,
            'slug'         => $slug,
            'excerpt'      => $excerpt,
            'content'      => $content,
            'cover_image'  => $coverPath,
            'status'       => $status,
            'published_at' => $publishedAt,
        ]);

        $tagIds = $this->resolveTags($payload['tags'] ?? null);
        if (!empty($tagIds)) {
            $article->tags()->attach($tagIds);
        }

        return $article->fresh('tags');
    }

    /**
     * 更新文章（仅更新传入的字段，其余保持）。
     */
    public function update(Article $article, array $payload): Article
    {
        $updates = [];

        if (isset($payload['title'])) {
            $updates['title'] = $payload['title'];
            if (!isset($payload['slug'])) {
                // 标题改了，slug 也跟着重算（除非显式传了）
                $newSlug = $this->resolveSlug(null, $payload['title']);
                if ($newSlug !== $article->slug) {
                    $updates['slug'] = $newSlug;
                }
            }
        }
        if (array_key_exists('slug', $payload) && $payload['slug']) {
            $updates['slug'] = $this->resolveSlug($payload['slug'], $payload['title'] ?? $article->title);
        }

        if (isset($payload['content'])) {
            $content = $payload['content'];
            if ($payload['fetch_remote_images'] ?? true) {
                $content = $this->fetchRemoteImages($content);
            }
            $updates['content'] = $content;
        }

        if (array_key_exists('excerpt', $payload)) {
            $updates['excerpt'] = $this->resolveExcerpt(
                $payload['excerpt'],
                $updates['content'] ?? $article->content
            );
        }

        if ($this->hasCoverImagePayload($payload)) {
            if ($article->cover_image) {
                Storage::disk('public')->delete($article->cover_image);
            }
            $updates['cover_image'] = $this->resolveCoverImage($payload);
        }

        if (isset($payload['status'])) {
            $wasPublished = $article->status === 'published';
            $updates['status'] = $payload['status'];
            if ($payload['status'] === 'published' && !$wasPublished) {
                $updates['published_at'] = $payload['published_at'] ?? now();
            }
        }
        if (isset($payload['published_at'])) {
            $updates['published_at'] = $payload['published_at'];
        }

        $article->update($updates);

        if (array_key_exists('tags', $payload)) {
            $article->tags()->sync($this->resolveTags($payload['tags']));
        }

        return $article->fresh('tags');
    }

    /**
     * 删除文章及其封面/标签关系。
     * 同时释放 slug（追加 _deleted_<ts>），允许后续相同标题重新发布。
     */
    public function delete(Article $article): void
    {
        if ($article->cover_image) {
            Storage::disk('public')->delete($article->cover_image);
        }
        $article->tags()->detach();
        $article->slug = $article->slug . '_deleted_' . time();
        $article->saveQuietly();
        $article->delete();
    }

    // ===================================================================
    // 各字段解析器
    // ===================================================================

    /**
     * slug 解析：
     *   1. 显式传入 → 校验唯一性后使用
     *   2. 含 CJK 字符 → 用拼音整词转换（"我的 API 文章" → "wo-de-api-wen-zhang"）
     *   3. 纯 ASCII → Str::slug
     *   4. 重复时自动补 -2/-3 后缀
     */
    protected function resolveSlug(?string $slug, string $title): string
    {
        if ($slug) {
            $base = Str::slug($slug);
        } elseif (preg_match('/\p{Han}/u', $title)) {
            try {
                $pinyin = Pinyin::sentence($title);
                $base = Str::slug($pinyin);
            } catch (\Throwable $e) {
                $base = Str::slug($title);
            }
        } else {
            $base = Str::slug($title);
        }

        if (!$base) {
            $base = 'article-' . Str::lower(Str::random(6));
        }

        $finalSlug = $base;
        $i = 1;
        while (Article::withTrashed()->where('slug', $finalSlug)->exists()) {
            $i++;
            $finalSlug = $base . '-' . $i;
            if ($i > 100) {
                $finalSlug = $base . '-' . Str::lower(Str::random(6));
                break;
            }
        }
        return $finalSlug;
    }

    /**
     * 自动截取 excerpt：去掉 markdown 后取前 120 字。
     */
    protected function resolveExcerpt(?string $excerpt, string $content): ?string
    {
        if ($excerpt) {
            return Str::limit($excerpt, 500, '...');
        }

        $plain = strip_tags($content);
        $plain = preg_replace('/!\[[^\]]*\]\([^)]+\)/u', '', $plain);
        $plain = preg_replace('/\[([^\]]+)\]\([^)]+\)/u', '$1', $plain);
        $plain = preg_replace('/[#*`>_~\-=]+/u', '', $plain);
        $plain = preg_replace('/\s+/u', ' ', $plain);
        $plain = trim($plain);

        return $plain ? Str::limit($plain, 120, '...') : null;
    }

    /**
     * 解析发布时间：草稿留 null，已发布默认当前；可显式覆盖。
     */
    protected function resolvePublishedAt(?string $publishedAt, string $status): ?\DateTime
    {
        if ($publishedAt) {
            return new \DateTime($publishedAt);
        }
        return $status === 'published' ? now() : null;
    }

    /**
     * 解析 tags：支持 名称数组 / ID 数组 / 名称+ID 混合 / null。
     *   ["技术", "Python", 5]  →  全部转成 ID
     *   未存在的名称会被 firstOrCreate 自动建立。
     *
     * @return int[]
     */
    protected function resolveTags($tags): array
    {
        if (empty($tags)) {
            return [];
        }
        if (!is_array($tags)) {
            $tags = preg_split('/[,，;；]/u', (string) $tags);
        }

        $ids = [];
        foreach ($tags as $tag) {
            $tag = is_string($tag) ? trim($tag) : $tag;
            if ($tag === '' || $tag === null) {
                continue;
            }

            if (is_numeric($tag)) {
                if (Tag::whereKey((int) $tag)->exists()) {
                    $ids[] = (int) $tag;
                }
                continue;
            }

            $name = mb_substr((string) $tag, 0, 30);
            $tagSlug = Str::slug($name) ?: Str::slug(Pinyin::sentence($name));
            if (!$tagSlug) {
                $tagSlug = 'tag-' . Str::lower(Str::random(6));
            }
            $existing = Tag::where('slug', $tagSlug)->orWhere('name', $name)->first();
            if ($existing) {
                $ids[] = $existing->id;
                continue;
            }
            $maxOrder = (int) Tag::max('sort_order');
            $ids[] = Tag::create([
                'name'       => $name,
                'slug'       => $this->uniqueTagSlug($tagSlug),
                'sort_order' => $maxOrder + 1,
            ])->id;
        }
        return array_values(array_unique($ids));
    }

    protected function uniqueTagSlug(string $base): string
    {
        $slug = $base;
        $i = 1;
        while (Tag::where('slug', $slug)->exists()) {
            $i++;
            $slug = $base . '-' . $i;
        }
        return $slug;
    }

    /**
     * 是否在 payload 中提供了任意一种封面图来源。
     */
    protected function hasCoverImagePayload(array $payload): bool
    {
        return !empty($payload['cover_image'])
            || !empty($payload['cover_image_url'])
            || !empty($payload['cover_image_base64']);
    }

    /**
     * 解析封面图：支持 UploadedFile / URL / Base64 三种来源。
     * 落地后自动生成 webp sidecar。
     */
    protected function resolveCoverImage(array $payload): ?string
    {
        $path = null;
        if (isset($payload['cover_image']) && $payload['cover_image'] instanceof UploadedFile) {
            $path = $payload['cover_image']->store('articles', 'public');
        } elseif (!empty($payload['cover_image_url'])) {
            $path = $this->downloadImage($payload['cover_image_url'], 'articles');
        } elseif (!empty($payload['cover_image_base64'])) {
            $path = $this->saveBase64Image($payload['cover_image_base64'], 'articles');
        }

        if ($path) {
            app(\App\Services\ImageOptimizer::class)->makeWebpSidecar($path);
        }
        return $path;
    }

    /**
     * 把 markdown 中的所有外链图片下载到本地 storage 并替换 URL。
     */
    public function fetchRemoteImages(string $content): string
    {
        $localHost = parse_url(config('app.url'), PHP_URL_HOST);

        return preg_replace_callback(
            '/!\[([^\]]*)\]\((https?:\/\/[^)\s]+)([^)]*)\)/u',
            function ($m) use ($localHost) {
                $url = $m[2];
                $alt = $m[1];
                $rest = $m[3] ?? '';
                $imgHost = parse_url($url, PHP_URL_HOST);
                if ($imgHost === $localHost) {
                    return $m[0];
                }
                $path = $this->downloadImage($url, 'articles/inline');
                if (!$path) {
                    return $m[0];
                }
                $newUrl = Storage::disk('public')->url($path);
                return "![{$alt}]({$newUrl}{$rest})";
            },
            $content
        );
    }

    /**
     * 下载远程图片到 public storage，返回相对路径。
     * 优先用 cURL（PHP-FPM 出于安全考虑通常会关闭 allow_url_fopen，但 cURL 可用）。
     */
    protected function downloadImage(string $url, string $dir): ?string
    {
        try {
            if (function_exists('curl_init')) {
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS      => 5,
                    CURLOPT_TIMEOUT        => 20,
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_USERAGENT      => 'ydxred-blog/1.0',
                    CURLOPT_HTTPHEADER     => ['Accept: image/*'],
                    CURLOPT_SSL_VERIFYPEER => true,
                ]);
                $data = curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $err  = curl_error($ch);
                curl_close($ch);
                if ($data === false || $code >= 400) {
                    \Log::warning("downloadImage curl failed", ['url' => $url, 'code' => $code, 'err' => $err]);
                    return null;
                }
            } else {
                $ctx = stream_context_create([
                    'http' => ['timeout' => 15, 'header' => "User-Agent: ydxred-blog/1.0\r\nAccept: image/*\r\n"],
                    'https'=> ['timeout' => 15, 'header' => "User-Agent: ydxred-blog/1.0\r\nAccept: image/*\r\n"],
                ]);
                $data = @file_get_contents($url, false, $ctx);
                if ($data === false) {
                    \Log::warning("downloadImage fgc failed", ['url' => $url]);
                    return null;
                }
            }

            if (strlen($data) < 100) {
                \Log::warning("downloadImage: response too small", ['url' => $url, 'size' => strlen($data)]);
                return null;
            }
            if (strlen($data) > 50 * 1024 * 1024) {
                \Log::warning("downloadImage: too big", ['url' => $url, 'size' => strlen($data)]);
                return null;
            }
            $ext = $this->guessImageExt($data, $url);
            if (!$ext) {
                \Log::warning("downloadImage: cannot detect ext", ['url' => $url]);
                return null;
            }
            $filename = $dir . '/' . date('Y/m') . '/' . Str::random(20) . '.' . $ext;
            Storage::disk('public')->put($filename, $data);
            app(\App\Services\ImageOptimizer::class)->makeWebpSidecar($filename);
            return $filename;
        } catch (\Throwable $e) {
            \Log::warning("downloadImage exception", ['url' => $url, 'msg' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * 把 Base64 字符串解码并存储为图片。
     */
    protected function saveBase64Image(string $base64, string $dir): ?string
    {
        if (preg_match('/^data:image\/(\w+);base64,(.+)$/i', $base64, $m)) {
            $ext = strtolower($m[1]);
            $data = base64_decode($m[2], true);
        } else {
            $data = base64_decode($base64, true);
            $ext = $data !== false ? $this->guessImageExt($data, '') : null;
        }
        if (!$data || !$ext) {
            return null;
        }
        if (strlen($data) > 50 * 1024 * 1024) {
            return null;
        }
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }
        $filename = $dir . '/' . date('Y/m') . '/' . Str::random(20) . '.' . $ext;
        Storage::disk('public')->put($filename, $data);
        app(\App\Services\ImageOptimizer::class)->makeWebpSidecar($filename);
        return $filename;
    }

    /**
     * 根据图片二进制 magic-bytes 或 URL 后缀推断扩展名。
     */
    protected function guessImageExt(string $data, string $url): ?string
    {
        $sig = substr($data, 0, 12);
        if (str_starts_with($sig, "\xFF\xD8\xFF")) return 'jpg';
        if (str_starts_with($sig, "\x89PNG\r\n\x1A\n")) return 'png';
        if (str_starts_with($sig, "GIF87a") || str_starts_with($sig, "GIF89a")) return 'gif';
        if (str_starts_with($sig, "RIFF") && substr($data, 8, 4) === 'WEBP') return 'webp';
        if (str_starts_with(ltrim($sig), '<svg') || str_starts_with(ltrim($sig), '<?xml')) return 'svg';

        $urlExt = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
        if (in_array($urlExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
            return $urlExt === 'jpeg' ? 'jpg' : $urlExt;
        }
        return null;
    }
}
