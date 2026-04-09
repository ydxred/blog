<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'nickname',
        'email',
        'content',
        'status',
        'ip_address',
        'user_agent',
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
