<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'ip_address',
        'user_agent',
        'referer',
        'user_id',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
