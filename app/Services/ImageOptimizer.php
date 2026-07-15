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

            // 内容图（文章封面 articles/、图床 uploads/）右下角打域名水印；
            // 动态 posts/ 与头像 avatars/ 不打。放在 webp 生成前，让 webp 也带水印。
            if (preg_match('#^(articles|uploads)/#', $relativePath)) {
                $this->watermark($relativePath, $disk);
            }

            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            // 已经是 webp 或动图（不压）
            if (in_array($ext, ['webp', 'gif', 'svg'], true)) {
                return null;
            }

            // 解码前先卡像素维度：防止"压缩炸弹"（磁盘几十 KB 但标称超大分辨率）在
            // imagecreatefrom* 阶段按 W*H*4 分配巨量内存打爆 php-fpm。getimagesize 只读文件头，很便宜。
            $info = @getimagesize($fullPath);
            if ($info && ((int) $info[0] * (int) $info[1]) > 40_000_000) {
                \Log::warning('ImageOptimizer: 图片分辨率超上限，跳过 webp 生成', [
                    'path' => $relativePath, 'w' => $info[0], 'h' => $info[1],
                ]);
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

    /**
     * 给内容图右下角打半透明域名水印，原地覆盖原图。
     * jpg/png/webp 支持；gif/svg 与过小图跳过。失败静默（不阻断发文）。
     */
    public function watermark(string $relativePath, string $disk = 'public'): bool
    {
        if (! function_exists('imagettftext')) {
            return false;
        }
        $font = '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf';
        if (! is_file($font)) {
            return false;
        }

        try {
            $fullPath = Storage::disk($disk)->path($relativePath);
            if (! is_file($fullPath)) {
                return false;
            }

            $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
            $img = match ($ext) {
                'jpg', 'jpeg' => @imagecreatefromjpeg($fullPath),
                'png'         => @imagecreatefrompng($fullPath),
                'webp'        => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($fullPath) : null,
                default       => null, // gif/svg 等跳过
            };
            if (! $img) {
                return false;
            }

            $w = imagesx($img);
            $h = imagesy($img);
            // 图标级小图不打
            if ($w < 140 || $h < 90) {
                imagedestroy($img);
                return false;
            }

            $text = $this->watermarkText();
            $size = max(11, (int) round($w * 0.026));   // 字号随图宽
            $margin = max(9, (int) round($w * 0.018));

            $bbox = imagettfbbox($size, 0, $font, $text);
            $tw = abs($bbox[2] - $bbox[0]);
            $x = $w - $tw - $margin;
            $y = $h - $margin;                           // baseline 贴底

            imagealphablending($img, true);
            $shadow = imagecolorallocatealpha($img, 0, 0, 0, 75);       // 半透明黑阴影，浅色图也能看清
            $white  = imagecolorallocatealpha($img, 255, 255, 255, 50); // 半透明白字
            imagettftext($img, $size, 0, $x + 1, $y + 1, $shadow, $font, $text);
            imagettftext($img, $size, 0, $x, $y, $white, $font, $text);

            switch ($ext) {
                case 'png':
                    imagesavealpha($img, true);
                    imagepng($img, $fullPath, 6);
                    break;
                case 'webp':
                    imagesavealpha($img, true);
                    imagewebp($img, $fullPath, 85);
                    break;
                default: // jpg/jpeg
                    imagejpeg($img, $fullPath, 88);
            }
            imagedestroy($img);

            return true;
        } catch (\Throwable $e) {
            Log::warning('[ImageOptimizer] watermark failed: ' . $e->getMessage(), [
                'path' => $relativePath,
            ]);
            return false;
        }
    }

    /** 水印文字：取站点域名（去掉 www.），如 ydxred.com */
    protected function watermarkText(): string
    {
        $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'ydxred.com';
        return preg_replace('/^www\./i', '', $host);
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
