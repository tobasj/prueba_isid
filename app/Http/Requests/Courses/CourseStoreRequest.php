<?php

namespace App\Http\Requests\Courses;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\InstructorRole;

class CourseStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'instructor_id' => ['required','exists:users,id', new InstructorRole],
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'lessons' => ['array'],
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
