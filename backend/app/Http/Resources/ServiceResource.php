<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'long_description' => $this->when($request->routeIs('*.show'), $this->long_description),
            'icon' => $this->icon,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at?->format('F j, Y'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            
            // Short excerpt
            'short_excerpt' => Str::limit($this->short_description ?? '', 100),
        ];

        // Add icon_url only if it exists
        if (isset($this->icon_url)) {
            $data['icon_url'] = $this->icon_url;
        }

        // Add features only if relationship exists and is loaded
        if (method_exists($this->resource, 'features') && $this->relationLoaded('features')) {
            $data['features'] = $this->features;
            $data['features_count'] = $this->features->count();
        }

        // Add links safely
        $data['links'] = [];
        
        try {
            $data['links']['self'] = route('api.services.show', $this->slug);
        } catch (\Exception $e) {
            $data['links']['self'] = url("/api/services/{$this->slug}");
        }
        
        try {
            $data['links']['api_show'] = route('api.services.show', $this->id);
        } catch (\Exception $e) {
            $data['links']['api_show'] = url("/api/services/{$this->id}");
        }

        return $data;
    }
}