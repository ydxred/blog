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
        $wechatSecretSet = Setting::get('wechat_app_secret', '') !== '';
        $wechatAutoSync = Setting::get('wechat_auto_sync', '0');

        return view('admin.settings.api_wechat', compact('user', 'wechatAppId', 'wechatSecretSet', 'wechatAutoSync'));
    }

    public function updateApiWechat(Request $request)
    {
        $request->validate([
            'wechat_app_id' => 'nullable|string|max:255',
            'wechat_app_secret' => 'nullable|string|max:255',
            'wechat_auto_sync' => 'nullable|in:0,1',
        ]);

        Setting::set('wechat_app_id', $request->input('wechat_app_id', ''));

        // AppSecret 加密存储；留空表示保留原值，不覆盖
        $newSecret = (string) $request->input('wechat_app_secret', '');
        if ($newSecret !== '') {
            Setting::set('wechat_app_secret', \Illuminate\Support\Facades\Crypt::encryptString($newSecret));
        }

        Setting::set('wechat_auto_sync', $request->input('wechat_auto_sync', '0'));

        return back()->with('success', '微信配置已保存');
    }

    public function site()
    {
        $siteTitle = Setting::get('site_title', config('app.name'));
        $siteTagline = Setting::get('site_tagline', config('app.tagline', '记录文字与日常'));

        return view('admin.settings.site', compact('siteTitle', 'siteTagline'));
    }

    public function updateSite(Request $request)
    {
        $validated = $request->validate([
            'site_title' => 'nullable|string|max:100',
            'site_tagline' => 'nullable|string|max:200',
        ]);

        Setting::set('site_title', $validated['site_title'] ?? '');
        Setting::set('site_tagline', $validated['site_tagline'] ?? '');

        return back()->with('success', '站点信息已更新');
    }

    public function generateApiToken()
    {
        $user = auth()->user();
        $plainToken = \Illuminate\Support\Str::random(60);
        $user->api_token = hash('sha256', $plainToken);
        $user->save();

        return back()
            ->with('success', 'API Token 已重新生成（仅本次显示，请妥善保存）')
            ->with('plain_api_token', $plainToken);
    }
}
