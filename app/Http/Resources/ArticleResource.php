<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ArticleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'slug'             => $this->slug,
            'url'              => url('/article/' . $this->slug),
            'excerpt'          => $this->excerpt,
            'cover_image'      => $this->cover_image,
            'cover_image_url'  => $this->cover_image ? Storage::disk('public')->url($this->cover_image) : null,
            'content'          => $this->when(
                $request->boolean('include_content', false) || $request->routeIs('api.articles.show'),
                $this->content
            ),
            'status'           => $this->status,
            'published_at'     => $this->published_at?->toIso8601String(),
            'created_at'       => $this->created_at?->toIso8601String(),
            'updated_at'       => $this->updated_at?->toIso8601String(),
            'tags'             => $this->whenLoaded('tags', function () {
                return $this->tags->map(fn($t) => [
                    'id'   => $t->id,
                    'name' => $t->name,
                    'slug' => $t->slug,
                ]);
            }),
            'author'           => [
                'id'   => $this->user_id,
                'name' => $this->whenLoaded('user', fn() => $this->user?->name),
            ],
        ];
    }
}
