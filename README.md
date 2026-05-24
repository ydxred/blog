# ydxred's Blog

这是一个基于 **Laravel 10** + **Tailwind CSS** + **Alpine.js** + **Vite** 构建的现代化个人博客系统。轻量、美观、响应式、SEO 友好，并提供完整的写作 / 动态 / 评论 / 后台管理 / 自动发布 API 能力。

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
- **按需加载**：首页 ~45 KB（gzip），文章详情 ~120 KB，后台编辑器才会加载 EasyMDE。
- **SEO**：每页 `meta description` / `canonical` / `og:*` / `twitter:card` 完整；文章详情输出 `BlogPosting` JSON-LD；自动生成 `og-default.png` 默认分享卡。
- **无障碍**：表单 `<label for>` + `autocomplete`、按钮 `aria-label` / `aria-pressed`、装饰 SVG `aria-hidden`。

---

## 🚀 环境要求

* PHP >= 8.1（推荐 8.3）
* MySQL >= 5.7（或 MariaDB >= 10.3）
* Nginx / OpenResty / Apache
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
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
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
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.jit_buffer_size=100M
opcache.jit=tracing
```

### 5. SEO / 分享卡
- 默认 OG 卡在 `public/og-default.png`，可换成自己的 1200×630 图。
- 文章详情自动生成 `BlogPosting` JSON-LD；列表 / 详情都有完整 `og:*` 与 `twitter:card`。

### 6. 想做 HTTPS + CDN 加速？
推荐 Let's Encrypt + Cloudflare 免费方案（接入 Cloudflare 后回源用 OpenResty 即可），续签可用 acme.sh / certbot。

---

*Powered by Laravel 10.*
