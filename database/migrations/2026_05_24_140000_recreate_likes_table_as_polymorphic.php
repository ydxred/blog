<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 重建 likes 表为多态结构（适配 LikeController）。
     * 原结构: id, user_id, post_id, created_at
     * 新结构: id, likeable_type, likeable_id, ip_address, created_at, updated_at
     * 兼容文章和动态两类点赞，并按 IP 去重。
     */
    public function up(): void
    {
        Schema::dropIfExists('likes');

        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->morphs('likeable');
            $table->string('ip_address', 45)->index();
            $table->timestamps();

            $table->unique(['likeable_type', 'likeable_id', 'ip_address'], 'like_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
