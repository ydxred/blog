# ydxred's Blog

这是一个基于 **Laravel 12** + **Tailwind CSS** + **Alpine.js** + **Vite** 构建的现代化个人博客系统。轻量、美观、响应式、SEO 友好、注重安全，提供完整的写作 / 动态 / 评论 / 点赞 / 站内搜索 / RSS / 后台管理 / 自动发布 API 能力。

> 🌐 在线运行：https://www.ydxred.com
>
> 📖 完整的开发者视角技术剖析（数据建模 → 发文核心 → 图片管线 → 缓存 → 安全 → 部署）见博客里的《深挖一个 Laravel 个人博客》。

## ✨ 核心功能

### 1. 博客文章 (Articles)
- **Markdown 编辑**：基于 EasyMDE，所见即所得；服务端用 league/commonmark 渲染，支持代码块、表格、任务列表等扩展。
- **封面 & 标签**：每篇文章可上传封面图、归类多个彩色标签。
- **状态管理**：草稿（Draft）/ 已发布（Published）双状态。
- **访问统计**：基于 IP + Session 自动去重，统计 PV / UV。
- **目录 / 高亮 / 代码块复制**：文章详情页自动生成 TOC、代码块语法高亮（highlight.js）、一键复制按钮、图片 Fancybox 预览。

### 2. 个人动态 / 树洞 (Posts / Moments)
- **类微博短内容**：快速发布短篇文字。
- **多图上传**：支持 JPG / PNG / GIF / WEBP / HEIC（iPhone 原图）。
- **表情 / 标签 / 加载更多**：发帖区按需懒加载 emoji-picker，列表无限滚动。

### 3. 互动与社交
- **评论系统**：访客可对文章 / 动态留言。
- **评论审核**：后台支持待审核、已通过、已拒绝；自动记录评论者 **IP** 与 **User-Agent**。
- **点赞**：文章与动态点赞，多态关联，访客无登录也能点；服务端用 `aria-pressed` 状态反馈。
- **社会化分享**：微博 / QQ / 微信扫码 / Twitter。

### 4. 自动发布 API
- **后台生成 API Token**（绑定账号，可一键吊销 / 重置），不再写死 `.env`。
- **REST 风格**：`GET/POST/PUT/DELETE /api/articles`、`POST /api/posts`、`POST /api/upload`、`GET /api/tags`。
- **写文章像调微博 API 一样简单**：支持 tags 用名字数组（自动建标签）、cover_image 用 URL / Base64 / 文件、正文内远程图自动落地。
- **限速**：公开 60 r/m、鉴权 120 r/m。
- **单图最大 50 MB**（HEIC、Live Photo 友好）。
- 后台 `/admin/settings/api_wechat` 内置完整的中文调用文档 + curl / Python 示例。

### 5. 微信公众号联动（可选）
- 后台可配置 AppID / AppSecret，发布文章时尝试同步到个人公众号（受微信 API 限制，仅作可选项）。

### 6. 强大的管理后台
- **移动端适配**：完美适配手机与电脑。
- **数据仪表盘**：文章、动态、评论、PV / UV 折线图（Chart.js）。
- **标签管理**：自定义颜色 / 排序。
- **访客记录**：最近三个月访客 IP、UA、来源、地理。
- **系统设置**：一键修改网站名称、"关于我" Markdown 内容、API Key、WeChat 配置。

### 7. 用户系统
- 注册 / 登录 / 找回密码。
- **个人资料**：昵称、邮箱、简介、头像（支持最大 20 MB）。
- 支持彻底注销删除账号。

### 8. 前端工程化与 SEO
- **零 CDN 依赖**：highlight.js / Fancybox / tocbot / EasyMDE / Chart.js / emoji-picker / social-share 全部走 npm + Vite 打包，离线 / 内网 / Cloudflare 抖动都不影响。
- **字体本地化**：Noto Sans/Serif SC 通过 `@fontsource` 进 Vite，按 `unicode-range` 拆 100+ 分片懒加载，整站告别 Google Fonts 跨墙慢。
- **按需加载**：首页 ~45 KB（gzip），文章详情 ~120 KB，后台编辑器才会加载 EasyMDE。
- **SEO**：每页 `meta description` / `canonical` / `og:*` / `twitter:card` 完整；文章详情输出 `BlogPosting` JSON-LD；自动生成 `og-default.png` 默认分享卡。
- **无障碍**：表单 `<label for>` + `autocomplete`、按钮 `aria-label` / `aria-pressed`、装饰 SVG `aria-hidden`。

### 9. 性能加速 ⚡
- **HTML 微缓存**：OpenResty `fastcgi_cache` 缓存游客 GET 请求 30 秒，TTFB 从 ~80 ms → **~9 ms**（命中后纯 nginx 响应，零 PHP）。`/admin`、`/login`、有 `*_session` cookie 的请求自动 BYPASS。
- **Brotli 压缩**：OpenResty 自编译 `ngx_brotli` 静态模块，HTML 比 gzip 再小 **~20%**。客户端按 `Accept-Encoding` 自动协商 br / gzip / identity。
- **图片 WebP 协商**：上传时同步生成 `.webp` sidecar，nginx 根据 `Accept: image/webp` 自动协商，对应 png/jpg **节省 ~60% 带宽**（174 KB → 70 KB 实测）。
- **Vite 哈希资源 1 年 immutable**：`/build/*.js|css|woff2` 设 `Cache-Control: public, max-age=31536000, immutable`。
- **PHP 8.3 + OPcache 256 MB + JIT tracing 128 MB**、`realpath_cache 4M`。
- **PHP-FPM dynamic**：`max_children=20` `start_servers=4` `max_requests=500`，应对 4 倍并发。
- **浏览统计异步化**：`dispatchAfterResponse` 在响应发完后再写库，TTFB 不受影响。

### 10. 站内搜索 / RSS / Sitemap
- **站内搜索**：`/search` 全文搜索标题 / 摘要 / 正文，LIKE 通配符已转义防注入。
- **RSS 2.0**：`/rss` 输出最近 20 篇公开文章，`<head>` 内置自动发现链接。
- **Sitemap**：`/sitemap.xml` 收录全部公开文章与标签页，`robots.txt` 指向它。

### 11. 图片处理管线
- **域名水印**：文章封面与正文内联图右下角自动打半透明域名水印（动态配图、头像不打）。
- **WebP + 尺寸压缩**：解码前 `getimagesize` 卡像素上限防"解压炸弹"，生成 WebP sidecar，超大边长自动缩放。
- **多来源封面**：上传文件 / 公网 URL（带 SSRF 防护）/ Base64 三选一。

### 12. 安全加固 🔒
- **上传白名单**：只认真实图片 magic-bytes（不信扩展名、不信声明的 MIME），SVG / HTML / 任意扩展名一律拒——防存储型 XSS。
- **SSRF 防护**：下载远程图时校验目标 IP 非私网 / 回环 / 保留段，禁跟随重定向、限定协议。
- **CSRF + 限流**：全站 CSRF；登录 / 评论 / API 各有节流。
- **PHP 硬化**：FPM 层 `disable_functions` 禁命令执行族 + 关 FFI（CLI 不受限，`artisan`/`composer` 照常）。
- **Nginx 层**：`.env` / `.git` / `vendor` 拒；storage 与 webroot 下的 `.php` 均不执行——webshell 落地也跑不起来。
- **前台展示开关**：文章除草稿 / 发布外，还有独立的"前台可见"开关，管理员可发布但对游客隐藏。
- **自定义错误页**：404（像素动画）/ 419 / 500 / 503 全部自定义，去掉框架默认页指纹。

---

## 🚀 环境要求

* **PHP 8.2+**（Laravel 12 最低要求；推荐 8.3，启用 OPcache + JIT）
* MySQL >= 5.7（或 MariaDB >= 10.3）
* Nginx / OpenResty / Apache（OpenResty + `ngx_brotli` 可获最佳压缩）
* Composer 2.x
* **Node.js >= 18 & NPM**（生产部署也必须，前端要 `npm run build` 一次）

---

## 🛠️ 部署教程

### 方式一：服务器全新环境快速部署 (推荐)

#### 1. 安装基础环境（以 Ubuntu 22.04 为例）
```bash
sudo apt update
sudo apt install -y php-fpm php-cli php-mysql php-mbstring php-xml php-curl \
                    php-zip php-gd php-bcmath unzip mariadb-server curl nodejs npm
```

#### 2. 安装 Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 3. 配置数据库
```bash
sudo systemctl start mariadb
sudo mysql -e "CREATE DATABASE blog DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER '你的专属数据库用户名'@'localhost' IDENTIFIED BY '你的强密码';"
sudo mysql -e "GRANT ALL PRIVILEGES ON blog.* TO '你的专属数据库用户名'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

> 出于安全考虑，**不要直接使用 root 账号**连接业务数据库，请单独建低权限专用账号。

#### 4. 获取代码 & 安装依赖
```bash
cd /var/www/blog

composer install --optimize-autoloader --no-dev

cp .env.example .env
php artisan key:generate
```

#### 5. 编辑 `.env`
```ini
APP_NAME="ydxred"
APP_ENV=production
APP_KEY=base64:你的密钥
APP_DEBUG=false
APP_URL=https://www.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog
DB_USERNAME=你的专属数据库用户名
DB_PASSWORD=你的数据库密码
```

#### 6. 编译前端资源（**生产环境必须**）
```bash
npm ci
npm run build
```
这一步会把 Vite 入口（`resources/js/*.js`、`resources/css/app.css`）打包到 `public/build/`，浏览器从这里加载，所有 JS / CSS 100% 本地化无 CDN。

#### 7. 运行迁移 + 软链接
```bash
php artisan migrate --force
php artisan storage:link
```

#### 8. 缓存优化
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 9. 设置目录权限
```bash
sudo chown -R www-data:www-data /var/www/blog/storage /var/www/blog/bootstrap/cache /var/www/blog/public
sudo chmod -R 775 /var/www/blog/storage /var/www/blog/bootstrap/cache
```

#### 10. 配置 Nginx
```nginx
server {
    listen 80;
    server_name www.yourdomain.com;
    root /var/www/blog/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    # 大图上传（50 MB 单图 + 余量）
    client_max_body_size 70M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # 屏蔽源码目录直接访问
    location ~* /(vendor|composer|database|app|bootstrap|tests|config|routes|resources)/ {
        deny all;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

> 同时把 `php.ini` 的 `upload_max_filesize`、`post_max_size` 都改到 **55M / 60M**，并重启 PHP-FPM。

---

### 方式二：本地开发调试

```bash
git clone <repository_url>
cd blog

composer install
npm install

cp .env.example .env
php artisan key:generate

# 配置好本地数据库后
php artisan migrate

# 两个终端同时跑
npm run dev          # 终端 A：Vite 实时编译
php artisan serve    # 终端 B：本地 PHP 服务
```

---

## 🔌 自动发布 API 快速上手

### 1. 在后台生成 Token
登录后台 → **系统设置 → API & 微信** → 点击「生成 API Token」，复制返回的 token（只显示一次）。

### 2. 发一篇文章（Python）
```python
import requests

API = "https://www.yourdomain.com/api/articles"
TOKEN = "粘贴你的 Token"

r = requests.post(API,
    headers={"Authorization": f"Bearer {TOKEN}"},
    json={
        "title": "我用 API 一行就发了这篇文章",
        "content": "# Hello\n\n这是正文 Markdown，可以直接放 ![图](https://example.com/x.png) 远程图，服务端会自动下载落地。",
        "tags": ["生活", "技术"],                  # 名字即可，标签不存在自动建
        "cover_image_url": "https://example.com/cover.jpg",  # 或 cover_image_base64 或 multipart 文件
        "status": "published",
    },
)
print(r.json())
```

### 3. curl 示例
```bash
curl -X POST https://www.yourdomain.com/api/articles \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"title":"快速发文","content":"正文","tags":["随手"],"status":"published"}'
```

更详细文档（含 CRUD、单独图床 `/api/upload`、动态 `/api/posts`、错误码）在后台 `/admin/settings/api_wechat` 页面有完整中文版。

---

## 🔧 常见问题与优化

### 1. 如何创建后台管理员账号？
先在前台注册一个普通账号，然后在服务器终端用 `tinker` 升级：
```bash
php artisan tinker
> $user = App\Models\User::where('email', 'your@email.com')->first();
> $user->role = 'admin';
> $user->save();
> exit
```
之后使用该邮箱登录即可进入 `/admin`。

### 2. 上传超清大图（手机原图 / HEIC）失败？
确保 3 处都已放开：
1. **Nginx**：`client_max_body_size 70M;`
2. **php.ini**：`upload_max_filesize = 55M`、`post_max_size = 60M`、`memory_limit >= 256M`
3. 若公开目录有 `.user.ini`，也要同步修改（它会**覆盖** php.ini）。
4. 改完重启 `nginx` + `php-fpm`。

### 3. 文章发出来 slug 是 `article-xxx` 而不是中文拼音？
项目自带 `overtrue/pinyin`，中文标题会自动转拼音 slug。如果失效，看是不是 `composer install --no-dev` 时把它当 dev 依赖排除掉了（项目内已设为生产依赖，正常 install 即可）。

### 4. 如何开启 PHP 性能加速（Opcache & JIT）？
新建 `/etc/php/8.3/fpm/conf.d/10-opcache-tuning.ini`：
```ini
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=256
opcache.interned_strings_buffer=32
opcache.max_accelerated_files=20000
opcache.validate_timestamps=1
opcache.revalidate_freq=2
opcache.fast_shutdown=1
opcache.save_comments=1
opcache.jit=tracing
opcache.jit_buffer_size=128M
```
再调高 PHP-FPM 并发：
```ini
; /etc/php/8.3/fpm/pool.d/www.conf
pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 500
```

### 5. 如何启用 OpenResty HTML 微缓存？
在 `http {}` 块加：
```nginx
fastcgi_cache_path /var/cache/nginx_fcgi levels=1:2 keys_zone=microcache:32m
                   max_size=512m inactive=10m use_temp_path=off;
fastcgi_cache_key  "$scheme$request_method$host$request_uri";
fastcgi_cache_use_stale error timeout updating;
fastcgi_cache_lock on;
fastcgi_cache_background_update on;
```
在 `location ~ ^/index\.php` 内：
```nginx
set $skip_cache 0;
if ($request_method != GET)                          { set $skip_cache 1; }
if ($http_authorization)                             { set $skip_cache 1; }   # 带 token 的鉴权请求不缓存
if ($request_uri ~* "^/api/")                        { set $skip_cache 1; }   # /api 私有响应绝不进缓存(否则越权泄露)
if ($http_cookie ~* "ydxred_session|remember_")      { set $skip_cache 1; }   # 带会话的不缓存
if ($request_uri ~* "/admin|/login|/logout|/profile|/likes/status|/like/") { set $skip_cache 1; }
fastcgi_cache microcache;
fastcgi_cache_valid 200 30s;
fastcgi_cache_bypass $skip_cache;
fastcgi_no_cache     $skip_cache;
fastcgi_ignore_headers Cache-Control Expires Set-Cookie Vary;
# ⚠️ 只在可缓存的匿名响应(skip_cache=0)上剥 Set-Cookie；
# 千万别用 `fastcgi_hide_header Set-Cookie;` 无条件剥——会把登录用户的会话 Cookie 也剥掉，全站 419。
if ($skip_cache = 0) { more_clear_headers 'Set-Cookie'; }
add_header X-Cache-Status $upstream_cache_status always;
```

### 6. 如何启用 WebP 内容协商？
**应用层**：上传图后自动生成 `.webp` sidecar（项目已内置，见 `App\Services\ImageOptimizer`）。批量回填存量：
```bash
php artisan images:webp
# 或指定目录
php artisan images:webp articles,posts,uploads
```
**Nginx 端**：
```nginx
location ~* ^/storage/.*\.(jpe?g|png)$ {
    add_header Vary "Accept" always;
    expires 90d;
    set $webp_real "";
    if ($http_accept ~* "image/webp")     { set $webp_real "A"; }
    if (-f $request_filename.webp)         { set $webp_real "${webp_real}B"; }
    if ($webp_real = "AB")                 { rewrite ^(.+)$ $1.webp last; }
    try_files $uri =404;
}
```

### 7. SEO / 分享卡
- 默认 OG 卡在 `public/og-default.png`，可换成自己的 1200×630 图。
- 文章详情自动生成 `BlogPosting` JSON-LD；列表 / 详情都有完整 `og:*` 与 `twitter:card`。

### 8. 想做 HTTPS + CDN 加速？
推荐 Let's Encrypt + Cloudflare 免费方案（接入 Cloudflare 后回源用 OpenResty 即可），续签可用 acme.sh / certbot。

### 9. 性能基线（参考）
| 指标 | 优化前 | 优化后 |
|---|---|---|
| 源站 TTFB（文章详情） | ~80 ms | **9–13 ms** |
| `/build/*.js` Cache-Control | 30 d | **1 y immutable** |
| HTML 压缩（同一页面） | gzip 2080 B | **brotli 1844 B（-20%）** |
| 大 PNG 实际下载（支持 webp） | 174 KB | **70 KB（-60%）** |
| Google Fonts 跨墙依赖 | 有 | **无（本地化）** |
| PHP 版本 | 8.1 | **8.3 + JIT tracing 128 MB** |
| OPcache 命中率 | 默认 | >95% + JIT |
| FPM 并发上限 | 5 | **20** |

### 10. 安装 Brotli（OpenResty 自编译）
Brotli 模块需要静态编译进 nginx。简化步骤：
```bash
# 1) 装 brotli 系统库 + 编译依赖
sudo apt install -y libbrotli-dev libpcre3-dev libssl-dev zlib1g-dev

# 2) 下载与现有 OpenResty 同版本的源码（自行替换版本号）
cd /usr/local/src
wget https://openresty.org/download/openresty-1.29.2.4.tar.gz
tar xf openresty-1.29.2.4.tar.gz
git clone --depth 1 --recurse-submodules https://github.com/google/ngx_brotli

# 3) 用与原 nginx -V 一致的 configure 参数 + 追加 brotli
cd openresty-1.29.2.4
./configure --prefix=/usr/local/openresty --add-module=../ngx_brotli \
  --with-http_ssl_module --with-http_v2_module --with-http_realip_module \
  --with-pcre-jit --with-file-aio --with-threads ...   # 保留原有全部 --with-*
gmake -j$(nproc)

# 4) 用 mv 替换运行中的 binary（rename 操作 inode-safe）
sudo mv /usr/local/openresty/nginx/sbin/nginx{,.bak}
sudo cp build/nginx-*/objs/nginx /usr/local/openresty/nginx/sbin/nginx
sudo systemctl restart openresty
```
nginx.conf 在 `http {}` 块加：
```nginx
brotli on;
brotli_comp_level 5;
brotli_min_length 256;
brotli_static on;
brotli_types text/plain text/css text/xml application/javascript application/json
             application/xml font/woff font/woff2 image/svg+xml;
```

---

*Powered by Laravel 12.*
