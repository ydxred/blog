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
}
