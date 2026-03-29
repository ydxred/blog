<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->string('commentable_type')->after('id')->default('');
            $table->unsignedBigInteger('commentable_id')->after('commentable_type')->default(0);
            $table->index(['commentable_type', 'commentable_id']);
        });

        DB::table('comments')->update([
            'commentable_type' => 'App\\Models\\Post',
            'commentable_id' => DB::raw('post_id'),
        ]);

        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
            $table->dropColumn('post_id');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('post_id')->after('id')->default(0)->constrained()->cascadeOnDelete();
        });

        DB::table('comments')->update([
            'post_id' => DB::raw('commentable_id'),
        ]);

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex(['commentable_type', 'commentable_id']);
            $table->dropColumn(['commentable_type', 'commentable_id']);
        });
    }
};
