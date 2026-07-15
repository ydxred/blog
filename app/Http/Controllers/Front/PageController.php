<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class PageController extends Controller
{
    public function about()
    {
        $content = \Illuminate\Support\Facades\Cache::remember('about_content', 3600, function () {
            return Setting::get('about_content', '这里是关于我的介绍，请在后台修改。');
        });
        
        return view('front.about', compact('content'));
    }

    /**
     * 发文 API 调用文档（独立静态页，不套前台布局）。
     */
    public function apiDocs()
    {
        return response(
            file_get_contents(resource_path('docs/api-docs.html')),
            200,
            [
                'Content-Type' => 'text/html; charset=UTF-8',
                'X-Robots-Tag' => 'noindex, nofollow',
            ]
        );
    }
}
