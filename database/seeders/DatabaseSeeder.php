<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@blog.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'bio' => '博主',
        ]);

        $tags = [
            ['name' => '技术', 'slug' => 'tech', 'color' => '#3b82f6', 'sort_order' => 1],
            ['name' => '生活', 'slug' => 'life', 'color' => '#10b981', 'sort_order' => 2],
            ['name' => '随想', 'slug' => 'thoughts', 'color' => '#8b5cf6', 'sort_order' => 3],
            ['name' => '分享', 'slug' => 'share', 'color' => '#f59e0b', 'sort_order' => 4],
            ['name' => '日常', 'slug' => 'daily', 'color' => '#ef4444', 'sort_order' => 5],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }

        $posts = [
            ['content' => "欢迎来到我的微博客！这是第一条帖子。\n\n在这里我会分享技术心得、生活感悟和各种有趣的事情。", 'tags' => [1, 4]],
            ['content' => '今天学习了 Laravel 的多态关联，真的很优雅。Eloquent ORM 的设计哲学值得深入研究。', 'tags' => [1]],
            ['content' => "周末去了趟公园，天气真好。\n阳光、微风、咖啡，完美的一天。", 'tags' => [2, 5]],
        ];

        foreach ($posts as $postData) {
            $post = $admin->posts()->create([
                'content' => $postData['content'],
            ]);
            $post->tags()->attach($postData['tags']);
        }
    }
}
