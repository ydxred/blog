<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Setting;
use EasyWeChat\OfficialAccount\Application;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 微信公众号同步：统一从后台「接口与微信」设置读取凭据（而非 env，避免 config:cache 后失效），
 * AppSecret 以密文存储、用时解密。被后台「同步微信」按钮与文章发布自动同步共用。
 */
class WechatSyncService
{
    public function isConfigured(): bool
    {
        $c = $this->config();
        return !empty($c['app_id']) && !empty($c['secret']);
    }

    protected function config(): array
    {
        $secret = (string) Setting::get('wechat_app_secret', '');
        if ($secret !== '') {
            try {
                $secret = Crypt::decryptString($secret);
            } catch (\Throwable $e) {
                // 兼容历史明文存储的密钥
            }
        }

        return [
            'app_id'  => (string) Setting::get('wechat_app_id', ''),
            'secret'  => $secret,
            'token'   => '',
            'aes_key' => '',
        ];
    }

    /**
     * 同步文章到公众号草稿箱。
     *
     * @return array{success:bool,message:string}
     */
    public function pushArticleToDraft(Article $article): array
    {
        $config = $this->config();
        if (empty($config['app_id']) || empty($config['secret'])) {
            return ['success' => false, 'message' => '请先在「接口与微信」设置里配置公众号 AppID 和 AppSecret'];
        }

        try {
            $app = new Application($config);
            $client = $app->getClient();

            $thumbMediaId = $this->uploadImage($article->cover_image, $client);
            if (!$thumbMediaId) {
                return ['success' => false, 'message' => '封面图上传微信失败（草稿箱要求文章有封面图）'];
            }

            $articleData = [
                'title'                 => $article->title,
                'author'                => $article->user->name ?? 'ydxred',
                'digest'                => $article->excerpt ?: Str::limit(strip_tags($article->content), 120),
                'content'               => $article->content,
                'content_source_url'    => url('/article/' . $article->slug),
                'thumb_media_id'        => $thumbMediaId,
                'need_open_comment'     => 1,
                'only_fans_can_comment' => 0,
            ];

            $result = $client->postJson('cgi-bin/draft/add', ['articles' => [$articleData]])->toArray();

            if (isset($result['media_id'])) {
                $article->wechat_media_id = $result['media_id'];
                $article->saveQuietly(); // 静默保存，避免再次触发发布钩子造成递归
                return ['success' => true, 'message' => '成功同步到微信草稿箱！Media ID: ' . $result['media_id']];
            }

            Log::error('Wechat sync failed', $result);
            return ['success' => false, 'message' => '同步失败：' . ($result['errmsg'] ?? '未知错误')];
        } catch (\Throwable $e) {
            Log::error('Wechat sync error: ' . $e->getMessage());
            return ['success' => false, 'message' => '同步发生异常：' . $e->getMessage()];
        }
    }

    protected function uploadImage(?string $imagePath, $client): ?string
    {
        if (!$imagePath) {
            return null;
        }
        $fullPath = storage_path('app/public/' . $imagePath);
        if (!file_exists($fullPath)) {
            return null;
        }

        try {
            $result = $client->withFile($fullPath, 'media')->post('cgi-bin/material/add_material', [
                'query' => ['type' => 'image'],
            ])->toArray();

            return $result['media_id'] ?? null;
        } catch (\Throwable $e) {
            Log::error('Upload image to wechat error: ' . $e->getMessage());
            return null;
        }
    }
}
