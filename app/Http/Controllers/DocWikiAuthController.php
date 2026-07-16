<?php

namespace App\Http\Controllers;

class DocWikiAuthController extends Controller
{
    /**
     * nginx auth_request 的鉴权闸:仅返回 200(放行)或 403(拒绝)。
     * 绝不重定向——auth_request 收到 3xx 会让主请求 500。
     * 只有登录且为管理员才能访问 /doc_wiki 静态知识库。
     */
    public function check()
    {
        return (auth()->check() && auth()->user()->isAdmin())
            ? response('', 200)
            : response('', 403);
    }
}
