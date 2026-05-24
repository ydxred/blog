<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * 图片优化服务
 *
 * 上传后异步/同步生成 WebP 副本：
 *   /storage/foo.jpg     原图（保留，旧浏览器兼容）
 *   /storage/foo.jpg.webp 新格式（前端 <picture> 优先用）
 *
 * 用法：app(ImageOptimizer::class)->makeWebpSidecar($relativePath, 'public');
 *
 * Nginx 端可加 try_files $uri.webp $uri 实现 Accept: image/webp 自动协商，
 * 但为了兼容 PHP/Laravel 的 ?id=xxx 与 storage:link 链路，本项目用 .webp 后缀显式访问。
 */
class ImageOptimizer
{
    /**
     * 为指定图片生成 .webp 副本，返回 相对路径（成功）或 null（失败/不支持）
     */
    public function makeWebpSidecar(string $relativePath, string $disk = 'public', int $quality = 82): ?string
    {
        // GD 是否支持 webp
        if (! function_exists('imagewebp')) {
            return null;
        }

        try {
            $fullPath = Storage::disk($disk)->path($relativePath);
            if (! is_file($fullPath)) {
                return null;
            }

            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            // 已经是 webp 或动图（不压）
            if (in_array($ext, ['webp', 'gif', 'svg'], true)) {
                return null;
            }

            // 读图
            $img = match ($ext) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($fullPath),
                'png'         => @imagecreatefrompng($fullPath),
                'bmp'         => function_exists('imagecreatefrombmp') ? @imagecreatefrombmp($fullPath) : null,
                default       => null,
            };
            if (! $img) {
                return null;
            }

            // PNG 透明通道
            if ($ext === 'png') {
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }

            $webpRel = $relativePath . '.webp';
            $webpFull = Storage::disk($disk)->path($webpRel);

            // 控制最大边长（>2000 太大没必要，缩小一半节省空间）
            [$w, $h] = [imagesx($img), imagesy($img)];
            $maxSide = 2400;
            if ($w > $maxSide || $h > $maxSide) {
                $ratio = $maxSide / max($w, $h);
                $newW = (int) ($w * $ratio);
                $newH = (int) ($h * $ratio);
                $resized = imagecreatetruecolor($newW, $newH);
                if ($ext === 'png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                }
                imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $w, $h);
                imagedestroy($img);
                $img = $resized;
            }

            imagewebp($img, $webpFull, $quality);
            imagedestroy($img);

            return $webpRel;
        } catch (\Throwable $e) {
            Log::warning('[ImageOptimizer] webp failed: ' . $e->getMessage(), [
                'path' => $relativePath,
            ]);
            return null;
        }
    }

    /** 批量生成（命令行用）：扫指定目录下未生成 webp 的图 */
    public function backfillDirectory(string $dirRel, string $disk = 'public'): array
    {
        $files = Storage::disk($disk)->allFiles($dirRel);
        $done = $skip = 0;
        foreach ($files as $f) {
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            if (! in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
                $skip++;
                continue;
            }
            if (Storage::disk($disk)->exists($f . '.webp')) {
                $skip++;
                continue;
            }
            $this->makeWebpSidecar($f, $disk) ? $done++ : $skip++;
        }
        return ['done' => $done, 'skip' => $skip, 'total' => count($files)];
    }
}
