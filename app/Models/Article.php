<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'status',
        'is_visible',
        'views_count',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_visible' => 'boolean',
    ];

    protected static function booted(): void
    {
        // 文章「变为已发布」时（新建即发布 / 草稿转发布），若开启了微信自动同步且尚未同步过，
        // 在响应返回后异步推送到公众号草稿箱（失败不影响发文）。点赞、切换展示等其它保存不触发。
        static::saved(function (self $article) {
            if ($article->status !== 'published' || !empty($article->wechat_media_id)) {
                return;
            }
            if (!$article->wasRecentlyCreated && !$article->wasChanged('status')) {
                return;
            }
            if (Setting::get('wechat_auto_sync', '0') !== '1') {
                return;
            }

            $service = app(\App\Services\WechatSyncService::class);
            if (!$service->isConfigured()) {
                return;
            }

            $id = $article->id;
            dispatch(function () use ($id, $service) {
                $fresh = self::find($id);
                if ($fresh) {
                    $service->pushArticleToDraft($fresh);
                }
            })->afterResponse();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function approvedComments()
    {
        return $this->morphMany(Comment::class, 'commentable')->where('status', 'approved');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }

    /**
     * 前台展示开关：游客只看到 is_visible 的文章；已登录管理员可看到全部（含隐藏），
     * 方便自己在前台预览。与 published() 组合使用。
     */
    public function scopeVisible($query)
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
            return $query;
        }

        return $query->where('is_visible', true);
    }

    public function getReadTimeAttribute(): int
    {
        return max(1, (int) ceil(mb_strlen(strip_tags($this->content)) / 400));
    }
}
