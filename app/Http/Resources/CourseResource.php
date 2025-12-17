<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'instructor' => new UserResource($this->instructor),
            'lessons' => LessonResource::collection($this->lessons),
            'average_rating' => round($this->comments_avg_rating ?? 0, 2),
            'ratings_count' => $this->comments_count ?? 0
        ];
    }
}
