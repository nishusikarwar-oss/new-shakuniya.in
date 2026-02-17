<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'short_excerpt' => $this->getShortExcerpt(100),
            'content' => $this->when($request->routeIs('api.blogs.show'), $this->content),
            'thumbnail' => $this->thumbnail,
            'thumbnail_url' => $this->thumbnail_url,
            'author' => $this->author,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at->format('F j, Y'),
            'created_at_human' => $this->created_at->diffForHumans(),
            
            // Metadata
            'has_thumbnail' => $this->hasThumbnail(),
            'word_count' => $this->when($request->routeIs('api.blogs.show'), function () {
                return str_word_count(strip_tags($this->content));
            }),
            'read_time' => $this->when($request->routeIs('api.blogs.show'), function () {
                $wordCount = str_word_count(strip_tags($this->content));
                $minutes = ceil($wordCount / 200); // Average reading speed: 200 words/minute
                return max(1, $minutes) . ' min read';
            }),

            // Links
            'links' => [
                'self' => route('api.blogs.show', $this->slug),
                'api_show' => route('api.blogs.show', $this->id),
                'web_url' => url("/blog/{$this->slug}"),
            ],
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->header('X-Powered-By', 'My Project API');
        $response->header('X-Resource-Type', 'Blog');
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function with(Request $request)
    {
        $additional = [];

        if ($request->routeIs('api.blogs.show')) {
            $additional['meta'] = [
                'description' => 'Detailed information about blog: ' . $this->title,
                'type' => 'article',
                'published_time' => $this->created_at->toISOString(),
            ];
        }

        return $additional;
    }
}