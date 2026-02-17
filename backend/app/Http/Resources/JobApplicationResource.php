<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobApplicationResource extends JsonResource
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
            'job_id' => $this->job_id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'message' => $this->message,
            'cv_file' => $this->cv_file,
            'cv_file_url' => $this->cv_file_url,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_formatted' => $this->created_at->format('F j, Y'),
            'created_at_human' => $this->created_at->diffForHumans(),
            
            // Job details (if loaded)
            'job' => new JobOpeningResource($this->whenLoaded('job')),
        ];
    }
}