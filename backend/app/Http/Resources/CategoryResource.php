<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_name' => $this->category_name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'status_text' => $this->status ? 'Active' : 'Inactive',
            'parent_id' => $this->parent_id,
            'order' => $this->order,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            
            // Relationships
            'parent' => new CategoryResource($this->whenLoaded('parent')),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
            'children_count' => $this->when($this->children_count, $this->children_count, 0),
            
            // Dates
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'created_at_formatted' => $this->created_at ? $this->created_at->format('F j, Y') : null,
            'created_at_human' => $this->created_at ? $this->created_at->diffForHumans() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            
            // Links
            'links' => [
                'self' => route('api.categories.show', $this->slug),
                'api_show' => route('api.categories.show', $this->id),
            ],
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request)
    {
        return [
            'meta' => [
                'api_version' => '1.0.0',
                'resource' => 'Category',
            ],
        ];
    }
}