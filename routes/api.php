<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PublishController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 公开接口（不需鉴权，仅 GET 公共信息）
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/tags', [PublishController::class, 'indexTags']);
});

// 需要 API Token 鉴权 + 限速
Route::middleware(['api.token', 'throttle:120,1'])->group(function () {
    // 文章 CRUD
    Route::get('/articles',          [PublishController::class, 'indexArticles']);
    Route::post('/articles',         [PublishController::class, 'storeArticle']);
    Route::get('/articles/{key}',    [PublishController::class, 'showArticle'])->name('api.articles.show');
    Route::match(['put', 'patch'], '/articles/{key}', [PublishController::class, 'updateArticle']);
    Route::delete('/articles/{key}', [PublishController::class, 'destroyArticle']);

    // 动态
    Route::post('/posts',  [PublishController::class, 'storePost']);

    // 单独图床
    Route::post('/upload', [PublishController::class, 'uploadImage']);
});
