@extends('admin.layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">API与微信配置</h2>
    </div>

    <!-- API Token 配置 -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">自动发布 API 密钥 (Token)</h3>
        <p class="text-sm text-gray-500 mb-6">用于自动化发文或发动态的接口鉴权。<strong class="text-red-500">出于安全考虑，Token 仅在生成时显示一次</strong>，数据库中只存哈希值，无法找回。</p>

        @if (session('plain_api_token'))
            <div class="mb-4 p-4 border-2 border-dashed border-amber-300 bg-amber-50 rounded-xl">
                <div class="flex items-start gap-3">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-amber-800 mb-2">🔑 新 Token 已生成（仅本次显示）：</p>
                        <code id="newToken" class="block bg-white border border-amber-200 rounded-lg px-3 py-2 text-sm font-mono break-all text-gray-800 select-all">{{ session('plain_api_token') }}</code>
                        <p class="text-xs text-amber-700 mt-2">请立即复制并妥善保存，刷新页面后将无法再次查看。</p>
                    </div>
                    <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('newToken').innerText); this.innerText='已复制';" class="px-4 py-2 bg-amber-500 text-white text-xs rounded-lg hover:bg-amber-600">复制</button>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.settings.api_wechat.generate_token') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Token 状态</label>
                <div class="flex gap-4">
                    <input type="text" readonly value="{{ $user->api_token ? '已生成（已加密存储，无法显示明文）' : '尚未生成' }}"
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
        <h3 class="text-lg font-semibold text-gray-800 mb-1">API 调用文档</h3>
        <p class="text-xs text-gray-500 mb-4">所有接口统一返回 JSON，结构为 <code>{success, message, data, errors}</code>。鉴权头部 <code>Authorization: Bearer &lt;Token&gt;</code>，限速 120 次/分钟。</p>
        <div class="prose prose-sm max-w-none text-gray-600">

            <!-- 接口列表 -->
            <h4 class="text-gray-800 font-medium mt-2 mb-2">接口一览</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="py-2 px-3 text-gray-800 font-medium">方法</th>
                            <th class="py-2 px-3 text-gray-800 font-medium">路径</th>
                            <th class="py-2 px-3 text-gray-800 font-medium">说明</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-mono text-xs">
                        <tr><td class="py-2 px-3 text-emerald-600">GET</td><td class="py-2 px-3">/api/tags</td><td class="py-2 px-3 font-sans">列出所有可用标签（公开，60 次/分钟）</td></tr>
                        <tr><td class="py-2 px-3 text-emerald-600">GET</td><td class="py-2 px-3">/api/articles</td><td class="py-2 px-3 font-sans">列出当前用户文章，支持 <code>?status=draft&amp;q=&amp;page=</code></td></tr>
                        <tr><td class="py-2 px-3 text-emerald-600">GET</td><td class="py-2 px-3">/api/articles/{id|slug}</td><td class="py-2 px-3 font-sans">查看文章详情</td></tr>
                        <tr><td class="py-2 px-3 text-indigo-600">POST</td><td class="py-2 px-3">/api/articles</td><td class="py-2 px-3 font-sans">发布文章 <span class="text-emerald-600">⭐</span></td></tr>
                        <tr><td class="py-2 px-3 text-amber-600">PUT</td><td class="py-2 px-3">/api/articles/{id|slug}</td><td class="py-2 px-3 font-sans">更新文章（仅传需要更新的字段）</td></tr>
                        <tr><td class="py-2 px-3 text-red-600">DELETE</td><td class="py-2 px-3">/api/articles/{id|slug}</td><td class="py-2 px-3 font-sans">删除文章</td></tr>
                        <tr><td class="py-2 px-3 text-indigo-600">POST</td><td class="py-2 px-3">/api/upload</td><td class="py-2 px-3 font-sans">上传图片到图床，返回可用 URL</td></tr>
                        <tr><td class="py-2 px-3 text-indigo-600">POST</td><td class="py-2 px-3">/api/posts</td><td class="py-2 px-3 font-sans">发布动态（短内容）</td></tr>
                    </tbody>
                </table>
            </div>

            <h4 class="text-gray-800 font-medium mt-6 mb-2">POST /api/articles 字段说明</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="py-2 px-3">参数</th>
                            <th class="py-2 px-3">类型</th>
                            <th class="py-2 px-3">必填</th>
                            <th class="py-2 px-3">说明</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr><td class="py-2 px-3 font-mono text-blue-600">title</td><td class="py-2 px-3">String</td><td class="py-2 px-3 text-red-500">是</td><td class="py-2 px-3">标题（≤200 字）。slug 不传时会自动用<strong>拼音</strong>生成，比如「我的文章」→ <code>wo-de-wen-zhang</code>。</td></tr>
                        <tr><td class="py-2 px-3 font-mono text-blue-600">content</td><td class="py-2 px-3">String</td><td class="py-2 px-3 text-red-500">是</td><td class="py-2 px-3">Markdown 正文。<strong>内文里的外链图片会自动下载落地</strong>，再也不怕原站挂掉。</td></tr>
                        <tr><td class="py-2 px-3 font-mono text-blue-600">status</td><td class="py-2 px-3">String</td><td class="py-2 px-3 text-gray-400">否</td><td class="py-2 px-3"><code>published</code> | <code>draft</code>，默认 <code>draft</code>。</td></tr>
                        <tr><td class="py-2 px-3 font-mono text-blue-600">excerpt</td><td class="py-2 px-3">String</td><td class="py-2 px-3 text-gray-400">否</td><td class="py-2 px-3">摘要。不传时<strong>自动从正文截取前 120 字</strong>。</td></tr>
                        <tr><td class="py-2 px-3 font-mono text-blue-600">slug</td><td class="py-2 px-3">String</td><td class="py-2 px-3 text-gray-400">否</td><td class="py-2 px-3">自定义 URL，不传则按上面规则生成；重复会自动加 -2/-3 等后缀。</td></tr>
                        <tr><td class="py-2 px-3 font-mono text-blue-600">published_at</td><td class="py-2 px-3">DateTime</td><td class="py-2 px-3 text-gray-400">否</td><td class="py-2 px-3">预约发布时间 ISO 8601，例如 <code>2026-12-01T08:00:00+08:00</code>。</td></tr>
                        <tr><td class="py-2 px-3 font-mono text-blue-600">tags</td><td class="py-2 px-3">Array</td><td class="py-2 px-3 text-gray-400">否</td><td class="py-2 px-3"><strong>名称/ID 混合</strong>都行。例如 <code>["技术", "Python", 5]</code>，名称不存在会<strong>自动创建</strong>。</td></tr>
                        <tr><td class="py-2 px-3 font-mono text-blue-600">cover_image</td><td class="py-2 px-3">File</td><td class="py-2 px-3 text-gray-400">否</td><td class="py-2 px-3">封面图文件上传（≤50MB，jpg/png/gif/webp）。</td></tr>
                        <tr><td class="py-2 px-3 font-mono text-blue-600">cover_image_url</td><td class="py-2 px-3">String</td><td class="py-2 px-3 text-gray-400">否</td><td class="py-2 px-3">封面图<strong>公网 URL</strong>，服务端自动下载入库。</td></tr>
                        <tr><td class="py-2 px-3 font-mono text-blue-600">cover_image_base64</td><td class="py-2 px-3">String</td><td class="py-2 px-3 text-gray-400">否</td><td class="py-2 px-3">封面图 <strong>Base64</strong>，支持 <code>data:image/png;base64,...</code> 或纯 base64。</td></tr>
                        <tr><td class="py-2 px-3 font-mono text-blue-600">fetch_remote_images</td><td class="py-2 px-3">Boolean</td><td class="py-2 px-3 text-gray-400">否</td><td class="py-2 px-3">是否自动下载正文外链图片，默认 <code>true</code>。</td></tr>
                    </tbody>
                </table>
            </div>

            <h4 class="text-gray-800 font-medium mt-6 mb-2">完整 Python 示例（极简版）</h4>
            <p class="text-xs text-gray-500 mb-2">不再需要查 tag ID、不再需要下载图片：</p>
            <div class="bg-gray-800 text-gray-200 p-4 rounded-lg overflow-x-auto text-xs font-mono leading-relaxed"><pre>import requests

API = "{{ url('/api') }}"
TOKEN = "替换为你后台生成的真实 Token"
H = {"Authorization": f"Bearer {TOKEN}", "Accept": "application/json"}

# 1) 一行发文：标签传名称、封面传 URL，所有麻烦事服务端搞定
r = requests.post(f"{API}/articles", headers=H, json={
    "title": "我用 API 自动发的中文文章",
    "content": "# 标题\n\n![网图也行](https://images.unsplash.com/photo-xxx.jpg)\n\n正文...",
    "status": "published",
    "tags": ["技术", "Python", "自动化"],       # 名称会自动创建
    "cover_image_url": "https://images.unsplash.com/photo-yyy.jpg",
})

print(r.json())
# {
#   "success": True,
#   "data": {
#     "id": 42,
#     "slug": "wo-yong-api-zi-dong-fa-de-zhong-wen-wen-zhang",   ← 自动拼音
#     "url": "https://www.ydxred.com/article/wo-yong-...",        ← 直接可访问
#     "cover_image_url": "https://www.ydxred.com/storage/...",   ← 封面已落地
#     ...
#   }
# }

# 2) 更新文章：只需要传要改的字段
requests.patch(f"{API}/articles/42", headers=H, json={
    "status": "draft",
})

# 3) 删除
requests.delete(f"{API}/articles/42", headers=H)

# 4) 列表 / 详情
print(requests.get(f"{API}/articles?status=published", headers=H).json())
print(requests.get(f"{API}/articles/wo-yong-...", headers=H).json())

# 5) 单独上传图片到图床（如果要先上传图、再在 markdown 引用 URL）
files = {"image": open("/tmp/x.jpg", "rb")}
print(requests.post(f"{API}/upload", headers=H, files=files).json())
# {"success": True, "data": {"url": "https://www.ydxred.com/storage/uploads/..."}}
</pre></div>

            <h4 class="text-gray-800 font-medium mt-6 mb-2">curl / bash 一键脚本</h4>
            <div class="bg-gray-800 text-gray-200 p-4 rounded-lg overflow-x-auto text-xs font-mono"><pre>TOKEN="你的token"
curl -X POST {{ url('/api/articles') }} \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Hello from curl",
    "content": "# 你好\n\n这是用 curl 发的文章。",
    "status": "published",
    "tags": ["脚本", "测试"]
  }'</pre></div>

            <h4 class="text-gray-800 font-medium mt-6 mb-2">统一响应结构</h4>
            <div class="bg-gray-800 text-gray-200 p-4 rounded-lg overflow-x-auto text-xs font-mono"><pre>// 成功
{
  "success": true,
  "message": "文章已创建",
  "data": { ... }
}

// 失败（如 422 参数错误）
{
  "success": false,
  "message": "参数校验失败",
  "errors": {"title": ["The title field is required."]}
}</pre></div>

        </div>
    </div>
</div>
@endsection