<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('articles', 'likes_count')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->unsignedInteger('likes_count')->default(0)->after('views_count');
            });
        }

        if (!Schema::hasColumn('posts', 'likes_count')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedInteger('likes_count')->default(0)->after('content');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');

        if (Schema::hasColumn('articles', 'likes_count')) {
            Schema::table('articles', function (Blueprint $table) {
                $table->dropColumn('likes_count');
            });
        }

        if (Schema::hasColumn('posts', 'likes_count')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('likes_count');
            });
        }
    }
};
