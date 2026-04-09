<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('wechat_media_id')->nullable()->after('status')->comment('微信图文消息media_id');
            $table->string('wechat_url')->nullable()->after('wechat_media_id')->comment('微信图文消息URL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('wechat_media_id');
            $table->dropColumn('wechat_url');
        });
    }
};
