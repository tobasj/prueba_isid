<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comments\CommentStoreRequest;
use App\Http\Resources\CommentResource;
use App\Models\Course;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Course $course)
    {
        $comments = $course->comments()->with('user')->latest()->paginate(15);
        return CommentResource::collection($comments);
    }

    public function store(CommentStoreRequest $request, Course $course)
    {
        $data = $request->validated();

        $comment = $course->comments()->create([
            'user_id' => $request->user()->id,
            'text' => $data['text'],
            'rating' => $data['rating'],
        ])->load('user');

        return new CommentResource($comment);
    }
}
