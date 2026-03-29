<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SliderCaptchaController extends Controller
{
    private const CW = 340;
    private const CH = 150;
    private const PW = 44;
    private const PH = 44;
    private const PR = 8;

    public function generate(Request $request)
    {
        $dir = storage_path('app/captcha');
        $files = glob($dir . '/*.jpg');
        if (empty($files)) {
            abort(500, 'No captcha backgrounds');
        }

        $target = random_int(self::PW + self::PR + 30, self::CW - self::PW - self::PR - 10);
        $pieceY = random_int(self::PR + 8, self::CH - self::PH - self::PR - 8);

        $request->session()->put('slider_captcha', [
            'target' => $target,
            'created_at' => now()->timestamp,
        ]);
        $request->session()->forget('slider_verified');

        $src = imagecreatefromjpeg($files[array_rand($files)]);
        $bg = imagecreatetruecolor(self::CW, self::CH);
        imagecopyresampled($bg, $src, 0, 0, 0, 0, self::CW, self::CH, imagesx($src), imagesy($src));
        imagedestroy($src);

        $margin = self::PR + 4;
        $regionX1 = max(0, $target - $margin);
        $regionY1 = max(0, $pieceY - $margin);
        $regionX2 = min(self::CW - 1, $target + self::PW + $margin);
        $regionY2 = min(self::CH - 1, $pieceY + self::PH + $margin);

        $pieceImgW = self::PW + self::PR + $margin;
        $piece = imagecreatetruecolor($pieceImgW, self::CH);
        imagesavealpha($piece, true);
        imagealphablending($piece, false);
        $trans = imagecolorallocatealpha($piece, 0, 0, 0, 127);
        imagefill($piece, 0, 0, $trans);
        imagealphablending($piece, true);

        for ($y = $regionY1; $y <= $regionY2; $y++) {
            for ($x = $regionX1; $x <= $regionX2; $x++) {
                $dist = $this->sdf($x - $target, $y - $pieceY);
                $localX = $x - $target + $margin;

                if ($localX < 0 || $localX >= $pieceImgW) continue;

                if ($dist <= -1.5) {
                    $c = imagecolorat($bg, $x, $y);
                    imagesetpixel($piece, $localX, $y, $c);
                } elseif ($dist <= 1.0) {
                    $c = imagecolorat($bg, $x, $y);
                    $r = ($c >> 16) & 0xFF;
                    $g = ($c >> 8) & 0xFF;
                    $b = $c & 0xFF;
                    if ($dist <= 0) {
                        imagesetpixel($piece, $localX, $y, $c);
                    } else {
                        $alpha = (int)(127 * ($dist / 1.0));
                        $ac = imagecolorallocatealpha($piece, $r, $g, $b, min(127, $alpha));
                        imagesetpixel($piece, $localX, $y, $ac);
                    }
                }

                // 白色边框
                if (abs($dist) < 1.8) {
                    $edgeAlpha = (int)(50 + 60 * (abs($dist) / 1.8));
                    $wc = imagecolorallocatealpha($piece, 255, 255, 255, min(127, $edgeAlpha));
                    imagesetpixel($piece, $localX, $y, $wc);
                }
            }
        }

        // 背景缺口
        for ($y = $regionY1; $y <= $regionY2; $y++) {
            for ($x = $regionX1; $x <= $regionX2; $x++) {
                $dist = $this->sdf($x - $target, $y - $pieceY);

                if ($dist <= 0) {
                    $c = imagecolorat($bg, $x, $y);
                    $r = ($c >> 16) & 0xFF;
                    $g = ($c >> 8) & 0xFF;
                    $b = $c & 0xFF;
                    $f = 0.45;
                    $nc = imagecolorallocate($bg, (int)($r * $f), (int)($g * $f), (int)($b * $f));
                    imagesetpixel($bg, $x, $y, $nc);
                } elseif ($dist < 2.5) {
                    $c = imagecolorat($bg, $x, $y);
                    $r = ($c >> 16) & 0xFF;
                    $g = ($c >> 8) & 0xFF;
                    $b = $c & 0xFF;
                    $t = $dist / 2.5;
                    $f = 0.45 + 0.55 * $t;
                    $nc = imagecolorallocate($bg, (int)($r * $f), (int)($g * $f), (int)($b * $f));
                    imagesetpixel($bg, $x, $y, $nc);
                }
            }
        }

        $token = Str::random(16);
        $pubDir = public_path('captcha-tmp');
        if (!is_dir($pubDir)) {
            mkdir($pubDir, 0755, true);
        }

        $this->cleanOldFiles($pubDir);

        imagejpeg($bg, $pubDir . '/' . $token . '_bg.jpg', 85);
        imagedestroy($bg);

        imagepng($piece, $pubDir . '/' . $token . '_piece.png', 7);
        imagedestroy($piece);

        return response()->json([
            'bg' => '/captcha-tmp/' . $token . '_bg.jpg',
            'piece' => '/captcha-tmp/' . $token . '_piece.png',
        ]);
    }

    private function cleanOldFiles(string $dir): void
    {
        $cutoff = time() - 600;
        foreach (glob($dir . '/*') as $file) {
            if (is_file($file) && filemtime($file) < $cutoff) {
                @unlink($file);
            }
        }
    }

    private function sdf(float $px, float $py): float
    {
        $w = self::PW;
        $h = self::PH;
        $r = self::PR;

        $dx = abs($px - $w / 2) - $w / 2;
        $dy = abs($py - $h / 2) - $h / 2;
        $rectDist = sqrt(max($dx, 0) ** 2 + max($dy, 0) ** 2) + min(max($dx, $dy), 0);

        $topDist = sqrt(($px - $w / 2) ** 2 + $py ** 2) - $r;
        $rightDist = sqrt(($px - $w) ** 2 + ($py - $h / 2) ** 2) - $r;

        return min($rectDist, $topDist, $rightDist);
    }

    public function check(Request $request)
    {
        $captcha = $request->session()->get('slider_captcha');
        if (!$captcha) {
            return response()->json(['ok' => false, 'msg' => '验证已过期，请刷新']);
        }

        $sliderX = (int) $request->input('slider_x', 0);
        $elapsed = (int) $request->input('slider_time', 0);
        $trail = $request->input('slider_trail', '');

        $target = $captcha['target'];
        $age = now()->timestamp - $captcha['created_at'];

        if ($age > 300) {
            $request->session()->forget('slider_captcha');
            return response()->json(['ok' => false, 'msg' => '验证已过期，请刷新']);
        }

        if (abs($sliderX - $target) > 4) {
            $request->session()->forget('slider_captcha');
            return response()->json(['ok' => false, 'msg' => '请对准缺口']);
        }

        if ($elapsed < 200) {
            $request->session()->forget('slider_captcha');
            return response()->json(['ok' => false, 'msg' => '操作异常']);
        }

        $points = array_values(array_filter(explode(',', $trail)));
        if (count($points) < 8) {
            $request->session()->forget('slider_captcha');
            return response()->json(['ok' => false, 'msg' => '操作异常']);
        }

        $diffs = [];
        for ($i = 1; $i < count($points); $i++) {
            $diffs[] = (int)$points[$i] - (int)$points[$i - 1];
        }

        // 匀速检测
        if (count($diffs) > 5) {
            $allSame = true;
            foreach ($diffs as $d) {
                if ($d !== $diffs[0]) { $allSame = false; break; }
            }
            if ($allSame) {
                $request->session()->forget('slider_captcha');
                return response()->json(['ok' => false, 'msg' => '操作异常']);
            }
        }

        // 方差检测
        if (count($diffs) > 10) {
            $avg = array_sum($diffs) / count($diffs);
            $variance = 0;
            foreach ($diffs as $d) {
                $variance += ($d - $avg) ** 2;
            }
            $variance /= count($diffs);
            if ($variance < 0.3) {
                $request->session()->forget('slider_captcha');
                return response()->json(['ok' => false, 'msg' => '操作异常']);
            }
        }

        // 回退检测——真人拖动通常有微小回退
        $hasReverse = false;
        foreach ($diffs as $d) {
            if ($d < 0) { $hasReverse = true; break; }
        }

        $token = Str::random(40);
        $request->session()->put('slider_verified', [
            'token' => $token,
            'at' => now()->timestamp,
        ]);
        $request->session()->forget('slider_captcha');

        return response()->json(['ok' => true, 'token' => $token]);
    }

    public static function isVerified(Request $request): bool
    {
        $v = $request->session()->get('slider_verified');
        if (!$v) return false;

        $token = $request->input('slider_token', '');
        if ($token !== $v['token']) return false;

        if (now()->timestamp - $v['at'] > 300) {
            $request->session()->forget('slider_verified');
            return false;
        }

        $request->session()->forget('slider_verified');
        return true;
    }
}
