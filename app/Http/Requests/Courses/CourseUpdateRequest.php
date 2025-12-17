<?php

namespace App\Http\Requests\Courses;

use Illuminate\Foundation\Http\FormRequest;

class CourseUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes','string','max:255'],
            'description' => ['sometimes','nullable','string'],
            'lessons' => ['sometimes','array'],
            'lessons.*.title' => ['required_with:lessons','string','max:255'],
            'lessons.*.video_url' => ['required_with:lessons','url'],
            'lessons.*.order' => ['nullable','integer','min:1'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
