@extends('admin.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">API与微信配置</h2>
    </div>

    <!-- API Token 配置 -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">自动发布 API 密钥 (Token)</h3>
        <p class="text-sm text-gray-500 mb-6">用于自动化发文或发动态的接口鉴权。请妥善保管，不要泄露。</p>

        <form action="{{ route('admin.settings.api_wechat.generate_token') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">您的当前 Token</label>
                <div class="flex gap-4">
                    <input type="text" readonly value="{{ $user->api_token ?? '尚未生成' }}"
                           class="flex-1 border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-600 focus:ring-0 focus:border-gray-200">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-500 text-white rounded-lg text-sm font-medium hover:bg-indigo-600 transition" onclick="return confirm('生成新 Token 会导致旧 Token 立即失效，确认要继续吗？')">
                        {{ $user->api_token ? '重新生成' : '立即生成' }}
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 微信公众号配置 -->
    <form action="{{ route('admin.settings.api_wechat.update') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        @csrf
        <h3 class="text-lg font-semibold text-gray-800 mb-2">微信公众号同步配置</h3>
        <p class="text-sm text-gray-500 mb-6">配置您的微信服务号 AppID 和 AppSecret。后续可配合自动化脚本实现文章发布后同步到微信草稿箱。</p>

        <div class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">开发者 ID (AppID)</label>
                <input type="text" name="wechat_app_id" value="{{ $wechatAppId }}"
                       class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="以 wx 开头的公众号 AppID">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">开发者密码 (AppSecret)</label>
                <input type="password" name="wechat_app_secret" value="{{ $wechatAppSecret }}"
                       class="w-full border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="公众号后台获取的 AppSecret">
            </div>

            <div class="flex items-center gap-3 mt-4">
                <input type="hidden" name="wechat_auto_sync" value="0">
                <input type="checkbox" id="wechat_auto_sync" name="wechat_auto_sync" value="1" {{ $wechatAutoSync == '1' ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                <label for="wechat_auto_sync" class="text-sm font-medium text-gray-700 cursor-pointer">发文章时开启自动同步到微信公众平台草稿箱功能</label>
            </div>
            
            <div class="pt-4">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                    保存微信配置
                </button>
            </div>
        </div>
    </form>

    <!-- API 使用文档 -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">API 调用文档（发给开发者的对接指南）</h3>
        <div class="prose prose-sm max-w-none text-gray-600 space-y-4">
            <p><strong>接口功能：</strong> 第三方应用/脚本自动化发布文章。</p>
            <p><strong>接口地址：</strong> <code>{{ url('/api/articles') }}</code></p>
            <p><strong>请求方式：</strong> <code>POST</code></p>
            <p><strong>数据格式：</strong> <code>multipart/form-data</code> (支持同时上传图片文件和文本字段)</p>
            
            <h4 class="text-gray-800 font-medium mt-4 mb-2">1. 鉴权头部 (Headers)</h4>
            <div class="bg-gray-800 text-gray-200 p-3 rounded-lg overflow-x-auto font-mono text-xs">
                Authorization: Bearer &lt;你的API_Token&gt;<br>
                Accept: application/json
            </div>
            <p class="text-xs text-gray-500 mt-1">* 请将 <code>&lt;你的API_Token&gt;</code> 替换为您在上方生成的真实 Token 值。</p>

            <h4 class="text-gray-800 font-medium mt-4 mb-2">2. 参数列表 (Form Data)</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="py-2 px-3 text-gray-800 font-medium">参数名</th>
                            <th class="py-2 px-3 text-gray-800 font-medium">类型</th>
                            <th class="py-2 px-3 text-gray-800 font-medium">必填</th>
                            <th class="py-2 px-3 text-gray-800 font-medium">参数说明</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-2 px-3 font-mono text-blue-600">title</td>
                            <td class="py-2 px-3">String</td>
                            <td class="py-2 px-3 text-red-500 font-medium">是</td>
                            <td class="py-2 px-3">文章标题（最长 200 字符）</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-mono text-blue-600">content</td>
                            <td class="py-2 px-3">String</td>
                            <td class="py-2 px-3 text-red-500 font-medium">是</td>
                            <td class="py-2 px-3"><strong>【重要】文章正文，请直接传入 Markdown 格式文本。</strong>发布后前台会自动渲染为漂亮的排版。</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-mono text-blue-600">status</td>
                            <td class="py-2 px-3">String</td>
                            <td class="py-2 px-3 text-red-500 font-medium">是</td>
                            <td class="py-2 px-3">发布状态。传 <code>published</code> (直接发布) 或 <code>draft</code> (保存为草稿)。</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-mono text-blue-600">excerpt</td>
                            <td class="py-2 px-3">String</td>
                            <td class="py-2 px-3 text-gray-400">否</td>
                            <td class="py-2 px-3">文章摘要（最长 500 字符）。不传则前端可能自动截取正文。</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-mono text-blue-600">cover_image</td>
                            <td class="py-2 px-3">File</td>
                            <td class="py-2 px-3 text-gray-400">否</td>
                            <td class="py-2 px-3">封面图片文件（支持 jpg, png, webp, gif，最大 5MB）。如果是自动化脚本，可以通过文件流上传。</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 font-mono text-blue-600">tags[]</td>
                            <td class="py-2 px-3">Array</td>
                            <td class="py-2 px-3 text-gray-400">否</td>
                            <td class="py-2 px-3">标签 ID 数组。如果文章带有多个标签，请传入多个该参数，例如：<code>tags[]=1</code> 和 <code>tags[]=3</code>。<br><span class="text-xs text-gray-500">注：标签ID请在左侧菜单“标签管理”中查看对应标签的编号。</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <h4 class="text-gray-800 font-medium mt-6 mb-2">3. 调用代码示例 (Python)</h4>
            <p class="text-xs text-gray-500 mb-2">以下是一段可以直接复制使用的 Python `requests` 库调用示例：</p>
            <div class="bg-gray-800 text-gray-200 p-4 rounded-lg overflow-x-auto text-xs font-mono leading-relaxed">
<pre>import requests

# 1. 接口地址配置
url = "{{ url('/api/articles') }}"
headers = {
    "Authorization": "Bearer 替换为你后台生成的真实Token", 
    "Accept": "application/json"
}

# 2. 准备文章数据 (支持 Markdown)
markdown_content = """
## 这是一个测试的 Markdown 标题
这是一段普通文本，**这是加粗文本**，*这是斜体*。

- 列表项 1
- 列表项 2

```python
print("Hello World!")
```
"""

data = {
    "title": "使用 API 和 Markdown 自动发布的文章",
    "content": markdown_content,  # 传入 Markdown 文本
    "status": "published",        # 'published' 立即发布，'draft' 存草稿
    "excerpt": "这是一篇包含 Markdown 内容的自动化测试文章。",
    
    # 标签 (假设后台有ID为 1 和 2 的标签)
    "tags[]": [1, 2]       
}

# 3. 准备封面图 (非必填，不需要图片可删除这部分)
# 'cover_image' 是后端接收的文件字段名
files = {
    "cover_image": ("cover.jpg", open("/path/to/your/image.jpg", "rb"), "image/jpeg")
}

# 4. 发起 POST 请求
# 注意：使用 requests 的 files 参数时，它会自动将 Content-Type 设为 multipart/form-data
response = requests.post(url, headers=headers, data=data, files=files)

# 5. 打印返回结果
print("状态码:", response.status_code)
print("返回结果:", response.json())
</pre>
            </div>

        </div>
    </div>
</div>
@endsection