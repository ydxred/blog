# ydxred's Blog

这是一个基于 **Laravel 10** + **Tailwind CSS** + **Alpine.js** 构建的现代化个人博客系统。旨在提供一个轻量、美观、响应式且功能齐全的个人写作与动态发布平台。

## ✨ 核心功能

### 1. 博客文章 (Articles)
- **支持 Markdown 编辑**：文章内容支持完整的 Markdown 语法排版。
- **文章封面与标签**：可以为每篇文章设置封面图，并归类到不同的标签下。
- **状态管理**：支持草稿 (Draft) 和已发布 (Published) 两种状态。
- **访问统计**：自动记录并统计文章的浏览量（基于 IP 和 Session 去重）。

### 2. 个人动态 / 树洞 (Posts / Moments)
- **类似微博的短内容**：支持快速发布短篇文字动态，适合记录生活碎片。
- **多图上传**：动态支持上传多张图片（支持 JPG、PNG、GIF、WEBP，甚至是 iOS 的 HEIC 格式）。
- **前台瀑布流展示**：动态在前台页面以美观的卡片流形式呈现。

### 3. 互动与社交
- **评论系统**：访客可以对文章和动态进行评论。
- **评论审核机制**：后台提供完整的评论管理功能（待审核、已通过、已拒绝）。
- **点赞功能**：支持对文章和动态点赞。

### 4. 强大的管理后台
- **移动端适配**：后台面板完美适配手机与电脑，随时随地管理博客。
- **数据仪表盘**：直观展示文章数、动态数、总阅读量和待审核评论。
- **标签管理**：自由创建和编辑分类标签，自定义标签颜色。
- **访客记录**：详细记录最近三个月的访客 IP、浏览器信息及来源。
- **系统设置**：支持后台一键修改网站名称及“关于我”页面的内容。

### 5. 用户系统
- 基础的注册/登录机制，带有安全防护。
- **个人资料管理**：用户可自行修改昵称、邮箱、个人简介和上传自定义头像。
- 支持彻底注销删除账号。

---

## 🚀 环境要求

* PHP >= 8.1
* MySQL >= 5.7 (或 MariaDB >= 10.3)
* Nginx / OpenResty / Apache
* Composer
* Node.js & NPM (仅用于本地编译前端资源)

---

## 🛠️ 部署教程

### 方式一：服务器全新环境快速部署 (推荐)

如果你有一台全新的 Ubuntu/Debian 服务器，可参考以下步骤配置：

#### 1. 安装基础环境 (以 Ubuntu 为例)
```bash
sudo apt update
sudo apt install -y php-fpm php-cli php-mysql php-mbstring php-xml php-curl php-zip php-gd unzip mariadb-server curl
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
sudo mysql -e "CREATE USER 'ydxred_blog'@'localhost' IDENTIFIED BY 'Ydxred_DB!2026';"
sudo mysql -e "GRANT ALL PRIVILEGES ON blog.* TO 'ydxred_blog'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

#### 4. 获取代码并安装依赖
将本项目代码克隆或上传至服务器（例如 `/var/www/blog` 目录）。

```bash
cd /var/www/blog

# 安装 PHP 依赖
composer install --optimize-autoloader --no-dev

# 复制配置文件
cp .env.example .env

# 生成应用密钥
php artisan key:generate
```

#### 5. 修改 `.env` 配置
编辑 `.env` 文件，填入你的数据库信息和网站 URL：
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
DB_USERNAME=ydxred_blog
DB_PASSWORD=你的数据库密码
```

#### 6. 运行迁移和创建软链接
```bash
php artisan migrate --force
php artisan storage:link
```

#### 7. 优化与缓存
为了提升线上运行速度，建议执行以下缓存命令：
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 8. 设置目录权限 (非常重要)
确保 Web 服务器（如 Nginx 的 `www-data` 用户）对 `storage` 和 `bootstrap/cache` 目录有写入权限：
```bash
sudo chown -R www-data:www-data /var/www/blog/storage /var/www/blog/bootstrap/cache
sudo chmod -R 775 /var/www/blog/storage /var/www/blog/bootstrap/cache
```

#### 9. 配置 Nginx
在你的 Nginx 配置（如 `/etc/nginx/sites-available/blog`）中加入以下内容：
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

    # 提高上传大小限制以支持大图片
    client_max_body_size 50M;

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

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

### 方式二：本地开发调试

1. 克隆代码：`git clone <repository_url>`
2. 安装后端依赖：`composer install`
3. 安装前端依赖：`npm install`
4. 复制环境配置：`cp .env.example .env`
5. 生成密钥：`php artisan key:generate`
6. 配置好本地数据库后运行迁移：`php artisan migrate`
7. 启动前端实时编译：`npm run dev`
8. 启动本地服务：`php artisan serve`

---

## 🔧 常见问题与优化

### 1. 如何创建后台管理员账号？
Laravel 没有内置独立的管理员表。你只需要先在网站前台注册一个普通账号，然后在服务器终端使用 `tinker` 将其升级为管理员：
```bash
php artisan tinker
> $user = App\Models\User::where('email', 'your@email.com')->first();
> $user->role = 'admin';
> $user->save();
> exit
```
之后使用该邮箱登录即可进入 `/admin` 管理后台。

### 2. 上传超清大图失败？
如果上传的图片（尤其是手机原图或 HEIC 格式）提示失败，请检查以下两处限制并调大：
1. **Nginx 配置**：`client_max_body_size 50M;`
2. **PHP 配置 (`php.ini`)**：
   - `upload_max_filesize = 50M`
   - `post_max_size = 50M`
修改后记得重启 Nginx 和 PHP-FPM。

### 3. 如何开启 PHP 性能加速 (Opcache & JIT)？
如果你觉得网站响应不够快，可以在 `php.ini` 中开启 Opcache 和 PHP 8+ 特有的 JIT：
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.jit_buffer_size=100M
opcache.jit=tracing
```

---
*Powered by Laravel 10.*