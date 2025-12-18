<?php

namespace App\Http\Requests\Comments;

use Illuminate\Foundation\Http\FormRequest;

class CommentUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['sometimes','string'],
            'rating' => ['sometimes','numeric','min:0','max:5'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
