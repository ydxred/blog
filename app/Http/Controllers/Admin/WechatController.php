<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\WechatSyncService;

class WechatController extends Controller
{
    /**
     * 同步文章到微信公众号草稿箱（后台「同步微信」按钮）。
     * 凭据统一由 WechatSyncService 从后台设置读取（加密存储），不再依赖 env。
     */
    public function syncArticle(Article $article, WechatSyncService $wechat)
    {
        $result = $wechat->pushArticleToDraft($article);

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
