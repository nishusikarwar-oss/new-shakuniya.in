<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'short_description' => $this->short_description,
            'icon' => $this->icon,
            'icon_url' => $this->icon_url,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at->format('F j, Y'),
            
            // Features (if loaded)
            'features' => ProductFeatureResource::collection($this->whenLoaded('features')),
            'features_count' => $this->whenLoaded('features', function() {
                return $this->features->count();
            }),
            
            // Links
            'links' => [
                'self' => route('api.products.show', $this->slug),
                'api_show' => route('api.products.show', $this->id),
            ],
        ];
    }
}