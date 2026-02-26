<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobOpeningResource extends JsonResource
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
            'description' => $this->description,
            'experience' => $this->experience,
            'positions' => $this->positions,
            'qualification' => $this->qualification,
            'location' => $this->location,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at->format('F j, Y'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            
            // Additional computed fields
            'experience_range' => $this->getExperienceRange(),
            
            // Count of applications (if loaded)
            'applications_count' => $this->whenLoaded('applications', function() {
                return $this->applications->count();
            }),
        ];
    }

    /**
     * Get experience range as array.
     *
     * @return array
     */
    private function getExperienceRange(): array
    {
        if (preg_match('/(\d+)-(\d+)/', $this->experience, $matches)) {
            return [
                'min' => (int) $matches[1],
                'max' => (int) $matches[2],
                'text' => $this->experience
            ];
        }
        
        return [
            'min' => 0,
            'max' => 0,
            'text' => $this->experience
        ];
    }
}