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
}
