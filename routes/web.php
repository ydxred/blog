<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\ArticleVisitController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Front\ArticleController;
use App\Http\Controllers\Front\CommentController;
use App\Http\Controllers\Front\ImageController;
use App\Http\Controllers\Front\PostController;
use App\Http\Controllers\Front\PageController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 首页 = 博客文章列表
Route::get('/', [ArticleController::class, 'index'])->name('home');
Route::get('/article/{article:slug}', [ArticleController::class, 'show'])->name('article.show');
Route::post('/article/{article}/comment', [CommentController::class, 'storeArticleComment'])->name('article.comment');

// 动态（微博）
Route::get('/moments', [PostController::class, 'index'])->name('moments');
Route::get('/post/{post}', [PostController::class, 'show'])->name('post.show');
Route::post('/post/{post}/comment', [CommentController::class, 'storePostComment'])->name('comment.store');

// 关于我
Route::get('/about', [PageController::class, 'about'])->name('about');

// 点赞
Route::post('/like/{type}/{id}', [\App\Http\Controllers\Front\LikeController::class, 'toggle'])->name('like.toggle');
Route::get('/likes/status', [\App\Http\Controllers\Front\LikeController::class, 'status'])->name('like.status');

Route::middleware('auth')->group(function () {
    Route::post('/post', [PostController::class, 'store'])->name('post.store');
    Route::delete('/post/{post}', [PostController::class, 'destroy'])->name('post.destroy');
    Route::post('/upload/image', [ImageController::class, 'upload'])->name('image.upload');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/api-token', [ProfileController::class, 'generateApiToken'])->name('profile.api_token');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 后台
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('tags', TagController::class)->except('show');
    Route::resource('articles', AdminArticleController::class)->except('show');

    Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
    Route::patch('/posts/{post}/toggle', [AdminPostController::class, 'toggleStatus'])->name('posts.toggle');
    Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::patch('/comments/{comment}/approve', [AdminCommentController::class, 'approve'])->name('comments.approve');
    Route::patch('/comments/{comment}/reject', [AdminCommentController::class, 'reject'])->name('comments.reject');
    Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('/visits', [ArticleVisitController::class, 'index'])->name('visits.index');

    Route::get('/settings/about', [SettingController::class, 'editAbout'])->name('settings.about');
    Route::post('/settings/about', [SettingController::class, 'updateAbout'])->name('settings.about.update');

    Route::get('/settings/api-wechat', [SettingController::class, 'apiWechat'])->name('settings.api_wechat');
    Route::post('/settings/api-wechat', [SettingController::class, 'updateApiWechat'])->name('settings.api_wechat.update');
    Route::post('/settings/api-wechat/generate-token', [SettingController::class, 'generateApiToken'])->name('settings.api_wechat.generate_token');

    Route::post('/upload/image', [\App\Http\Controllers\Admin\ImageUploadController::class, 'upload'])->name('upload.image');

    Route::post('/wechat/sync/{article}', [\App\Http\Controllers\Admin\WechatController::class, 'syncArticle'])->name('wechat.sync');
});

require __DIR__.'/auth.php';
