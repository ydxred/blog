<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Post;
use App\Models\Tag;
use App\Services\ArticlePublisher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PublishController extends Controller
{
    public function __construct(protected ArticlePublisher $publisher) {}

    // -------------------------------------------------------------------
    // Articles
    // -------------------------------------------------------------------

    /**
     * 列表（仅当前用户的）。
     * GET /api/articles?status=published&page=1
     */
    public function indexArticles(Request $request): JsonResponse
    {
        $query = Article::query()
            ->where('user_id', auth()->id())
            ->with('tags', 'user')
            ->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
        if ($q = $request->query('q')) {
            $query->where('title', 'like', "%{$q}%");
        }

        $articles = $query->paginate((int) ($request->query('per_page', 20)));

        return $this->ok([
            'items'      => ArticleResource::collection($articles),
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'last_page'    => $articles->lastPage(),
                'per_page'     => $articles->perPage(),
                'total'        => $articles->total(),
            ],
        ]);
    }

    /**
     * 详情。GET /api/articles/{id_or_slug}
     */
    public function showArticle(string $key): JsonResponse
    {
        $article = $this->findOwnedArticle($key);
        $article->load('tags', 'user');
        return $this->ok(new ArticleResource($article));
    }

    /**
     * 发布文章。POST /api/articles
     *
     * 字段（任意一个 cover 三选一）：
     *   title*, content* (markdown), status*(draft|published)
     *   excerpt?, slug?, published_at?
     *   tags?: ["标签A","标签B",5]    // 名称/ID 混合
     *   cover_image?: file
     *   cover_image_url?: 公网图片地址（自动下载落地）
     *   cover_image_base64?: data:image/png;base64,... 或纯 base64
     *   fetch_remote_images?: true     // 是否把 markdown 里的外链图下载（默认 true）
     */
    public function storeArticle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title'              => 'required|string|max:200',
            'content'            => 'required|string',
            'status'             => 'nullable|in:draft,published',
            'excerpt'            => 'nullable|string|max:500',
            'slug'               => 'nullable|string|max:200',
            'published_at'       => 'nullable|date',
            'cover_image'        => 'nullable|file|image|mimes:jpeg,jpg,png,gif,webp|max:51200',
            'cover_image_url'    => 'nullable|url|max:1000',
            'cover_image_base64' => 'nullable|string',
            'tags'               => 'nullable',
            'fetch_remote_images'=> 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->fail('参数校验失败', 422, $validator->errors());
        }

        $payload = [
            'title'              => $request->input('title'),
            'content'            => $request->input('content'),
            'status'             => $request->input('status', 'draft'),
            'excerpt'            => $request->input('excerpt'),
            'slug'               => $request->input('slug'),
            'published_at'       => $request->input('published_at'),
            'cover_image'        => $request->file('cover_image'),
            'cover_image_url'    => $request->input('cover_image_url'),
            'cover_image_base64' => $request->input('cover_image_base64'),
            'tags'               => $this->normalizeTagsInput($request),
            'fetch_remote_images'=> $request->boolean('fetch_remote_images', true),
        ];

        $article = $this->publisher->create($payload);

        return $this->ok(new ArticleResource($article), '文章已创建', 201);
    }

    /**
     * 更新文章。PUT/PATCH /api/articles/{id_or_slug}
     */
    public function updateArticle(Request $request, string $key): JsonResponse
    {
        $article = $this->findOwnedArticle($key);

        $validator = Validator::make($request->all(), [
            'title'              => 'sometimes|string|max:200',
            'content'            => 'sometimes|string',
            'status'             => 'sometimes|in:draft,published',
            'excerpt'            => 'nullable|string|max:500',
            'slug'               => 'nullable|string|max:200',
            'published_at'       => 'nullable|date',
            'cover_image'        => 'nullable|file|image|mimes:jpeg,jpg,png,gif,webp|max:51200',
            'cover_image_url'    => 'nullable|url|max:1000',
            'cover_image_base64' => 'nullable|string',
            'tags'               => 'nullable',
            'fetch_remote_images'=> 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->fail('参数校验失败', 422, $validator->errors());
        }

        $payload = [];
        foreach (['title', 'content', 'status', 'excerpt', 'slug', 'published_at',
                  'cover_image_url', 'cover_image_base64'] as $key) {
            if ($request->has($key)) {
                $payload[$key] = $request->input($key);
            }
        }
        if ($request->hasFile('cover_image')) {
            $payload['cover_image'] = $request->file('cover_image');
        }
        if ($request->has('tags')) {
            $payload['tags'] = $this->normalizeTagsInput($request);
        }
        if ($request->has('fetch_remote_images')) {
            $payload['fetch_remote_images'] = $request->boolean('fetch_remote_images', true);
        }

        $article = $this->publisher->update($article, $payload);

        return $this->ok(new ArticleResource($article), '文章已更新');
    }

    /**
     * 删除文章。DELETE /api/articles/{id_or_slug}
     */
    public function destroyArticle(string $key): JsonResponse
    {
        $article = $this->findOwnedArticle($key);
        $this->publisher->delete($article);
        return $this->ok(null, '文章已删除');
    }

    // -------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------

    /**
     * 列出所有可用标签。GET /api/tags
     */
    public function indexTags(): JsonResponse
    {
        $tags = Tag::orderBy('sort_order')->get(['id', 'name', 'slug']);
        return $this->ok($tags);
    }

    // -------------------------------------------------------------------
    // 图床
    // -------------------------------------------------------------------

    /**
     * 上传图片返回 URL（可用于 markdown 里）。POST /api/upload
     * 支持 multipart file 或 JSON URL/Base64。
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image'        => 'nullable|file|image|mimes:jpeg,jpg,png,gif,webp|max:51200',
            'image_url'    => 'nullable|url|max:1000',
            'image_base64' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->fail('参数校验失败', 422, $validator->errors());
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads', 'public');
        } elseif ($request->filled('image_url')) {
            $path = $this->publisher->fetchRemoteImages('![](' . $request->input('image_url') . ')');
            preg_match('/\(([^)]+)\)/', $path, $m);
            $url = $m[1] ?? null;
            if (!$url) {
                return $this->fail('图片下载失败', 502);
            }
            return $this->ok([
                'path' => null,
                'url'  => $url,
            ]);
        } elseif ($request->filled('image_base64')) {
            $payload = ['cover_image_base64' => $request->input('image_base64')];
            $reflection = new \ReflectionClass($this->publisher);
            $method = $reflection->getMethod('saveBase64Image');
            $method->setAccessible(true);
            $path = $method->invoke($this->publisher, $request->input('image_base64'), 'uploads');
            if (!$path) {
                return $this->fail('Base64 图片解码失败', 422);
            }
        } else {
            return $this->fail('请提供 image / image_url / image_base64 任一字段', 422);
        }

        return $this->ok([
            'path' => $path,
            'url'  => Storage::disk('public')->url($path),
        ]);
    }

    // -------------------------------------------------------------------
    // Posts (动态)
    // -------------------------------------------------------------------

    public function storePost(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:2000',
            'status'  => 'nullable|in:draft,published',
            'images'  => 'nullable|array|max:15',
            'images.*'=> 'string|url',
        ]);

        if ($validator->fails()) {
            return $this->fail('参数校验失败', 422, $validator->errors());
        }

        $status = $request->input('status', 'published');

        $post = Post::create([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
            'images'  => $request->input('images', []),
            'status'  => $status,
        ]);

        return $this->ok([
            'id'      => $post->id,
            'url'     => url('/post/' . $post->id),
            'content' => $post->content,
            'status'  => $post->status,
        ], '动态已发布', 201);
    }

    // -------------------------------------------------------------------
    // 内部工具
    // -------------------------------------------------------------------

    /**
     * 用 ID 或 slug 找当前用户拥有的文章。
     */
    protected function findOwnedArticle(string $key): Article
    {
        $query = is_numeric($key)
            ? Article::where('id', (int) $key)
            : Article::where('slug', $key);

        $article = $query->where('user_id', auth()->id())->first();
        abort_unless($article, 404, '文章不存在或无权操作');
        return $article;
    }

    /**
     * 兼容 tags / tags[] / tags=a,b,c / JSON 数组 多种形式。
     */
    protected function normalizeTagsInput(Request $request): array
    {
        if (!$request->has('tags')) {
            return [];
        }
        $tags = $request->input('tags');
        if (is_string($tags)) {
            $decoded = json_decode($tags, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            return preg_split('/[,，;；]/u', $tags) ?: [];
        }
        if (is_array($tags)) {
            return $tags;
        }
        return [];
    }

    /**
     * 统一成功响应。
     */
    protected function ok($data = null, string $message = 'ok', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    /**
     * 统一失败响应。
     */
    protected function fail(string $message, int $code = 400, $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }
}
