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
            'average_rating' => $this->average_rating,
            'ratings_count' => $this->ratings_count,
        ];
    }
}
