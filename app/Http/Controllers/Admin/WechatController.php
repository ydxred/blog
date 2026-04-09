<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use EasyWeChat\OfficialAccount\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WechatController extends Controller
{
    /**
     * 同步文章到微信公众号草稿箱
     */
    public function syncArticle(Article $article)
    {
        $config = [
            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID'),
            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET'),
            'token' => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN'),
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY'),
        ];

        if (empty($config['app_id']) || empty($config['secret'])) {
            return back()->with('error', '请先在.env中配置微信公众号信息');
        }

        try {
            $app = new Application($config);
            $client = $app->getClient();

            // 1. 上传封面图到微信获取 media_id
            $thumbMediaId = $this->uploadImageToWechat($article->cover_image, $client);
            if (!$thumbMediaId) {
                 return back()->with('error', '封面图上传微信失败');
            }

            // 2. 处理文章内容，如果有图片也需要替换为微信的图片URL (简单版暂不处理内容图片替换，或者你可以后续完善)
            // 这里为了演示，我们直接提交
            
            // 3. 构建图文数据
            $articleData = [
                'title' => $article->title,
                'author' => 'ydxred', // 或动态获取
                'digest' => $article->excerpt ?? Str::limit(strip_tags($article->content), 120),
                'content' => $article->content, // 注意：如果内容中含有外链图片，微信可能会过滤，严谨做法需要将内容中图片先上传到微信并替换URL
                'content_source_url' => url('/article/' . $article->slug),
                'thumb_media_id' => $thumbMediaId,
                'need_open_comment' => 1,
                'only_fans_can_comment' => 0,
            ];

            // 4. 调用微信草稿箱接口 (草稿箱 API - 新版)
            // https://developers.weixin.qq.com/doc/offiaccount/Draft_Box/Add_draft.html
            $response = $client->postJson('cgi-bin/draft/add', [
                'articles' => [
                    $articleData
                ]
            ]);

            $result = $response->toArray();

            if (isset($result['media_id'])) {
                $article->wechat_media_id = $result['media_id'];
                $article->save();
                return back()->with('success', '成功同步到微信草稿箱！Media ID: ' . $result['media_id']);
            } else {
                \Log::error('Wechat sync failed: ', $result);
                return back()->with('error', '同步失败：' . ($result['errmsg'] ?? '未知错误'));
            }

        } catch (\Exception $e) {
            \Log::error('Wechat sync error: ' . $e->getMessage());
            return back()->with('error', '同步发生异常：' . $e->getMessage());
        }
    }

    /**
     * 上传临时或永久素材获取 media_id
     */
    private function uploadImageToWechat($imagePath, $client)
    {
        if (!$imagePath) {
             // 如果没有封面，可能需要一张默认图片，这里我们为了简单，如果没有就不传或者返回错误
             return null;
        }

        $fullPath = storage_path('app/public/' . $imagePath);
        if (!file_exists($fullPath)) {
            return null;
        }

        try {
             // 使用永久素材上传，因为草稿箱引用的封面图通常需要永久素材
             $response = $client->withFile($fullPath, 'media')->post('cgi-bin/material/add_material', [
                 'query' => ['type' => 'image']
             ]);

             $result = $response->toArray();
             return $result['media_id'] ?? null;
        } catch (\Exception $e) {
            \Log::error('Upload image to wechat error: ' . $e->getMessage());
            return null;
        }
    }
}
