<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $request->user()->favorites()->firstOrCreate(['course_id' => $course->id]);
        return response()->noContent();
    }

    public function destroy(Request $request, Course $course)
    {
        $request->user()->favorites()->where('course_id', $course->id)->delete();
        return response()->noContent();
    }
}
