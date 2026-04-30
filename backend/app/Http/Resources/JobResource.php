<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category' => [
                'id' => $this->category_id,
                'name' => $this->whenLoaded('category', fn () => $this->category->name, 'Uncategorized'),
            ],
            'location' => $this->location,
            'work_type' => $this->work_type,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'deadline' => $this->deadline->format('Y-m-d'),
            'status' => $this->status,
            'employer_id' => $this->user_id,
            'user' => [
                'id' => $this->user_id,
                'name' => $this->whenLoaded('user', fn () => $this->user->name),
            ],
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
