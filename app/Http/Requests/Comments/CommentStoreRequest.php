<?php

namespace App\Http\Requests\Comments;

use Illuminate\Foundation\Http\FormRequest;

class CommentStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'text' => ['required','string'],
            'rating' => ['required','numeric','min:1','max:5'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
