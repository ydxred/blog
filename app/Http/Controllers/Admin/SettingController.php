<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function editAbout()
    {
        $content = Setting::get('about_content', '');
        return view('admin.settings.about', compact('content'));
    }

    public function updateAbout(Request $request)
    {
        $request->validate([
            'content' => 'nullable|string',
        ]);

        Setting::set('about_content', $request->input('content', ''));
        \Illuminate\Support\Facades\Cache::forget('about_content');

        return back()->with('success', '关于我页面内容已更新');
    }

    public function apiWechat()
    {
        $user = auth()->user();
        $wechatAppId = Setting::get('wechat_app_id', '');
        $wechatAppSecret = Setting::get('wechat_app_secret', '');
        $wechatAutoSync = Setting::get('wechat_auto_sync', '0');

        return view('admin.settings.api_wechat', compact('user', 'wechatAppId', 'wechatAppSecret', 'wechatAutoSync'));
    }

    public function updateApiWechat(Request $request)
    {
        $request->validate([
            'wechat_app_id' => 'nullable|string|max:255',
            'wechat_app_secret' => 'nullable|string|max:255',
            'wechat_auto_sync' => 'nullable|in:0,1',
        ]);

        Setting::set('wechat_app_id', $request->input('wechat_app_id', ''));
        Setting::set('wechat_app_secret', $request->input('wechat_app_secret', ''));
        Setting::set('wechat_auto_sync', $request->input('wechat_auto_sync', '0'));

        return back()->with('success', '微信配置已保存');
    }

    public function generateApiToken()
    {
        $user = auth()->user();
        $user->api_token = \Illuminate\Support\Str::random(60);
        $user->save();

        return back()->with('success', 'API Token 已重新生成');
    }
}
