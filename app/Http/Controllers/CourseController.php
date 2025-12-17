<?php

namespace App\Http\Controllers;

use App\Http\Requests\Courses\CourseStoreRequest;
use App\Http\Requests\Courses\CourseUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['instructor','lessons'])->paginate(15);

        return CourseResource::collection($courses);
    }

    public function store(CourseStoreRequest $request)
    {
        $data = $request->validated();

        $course = Course::create(Arr::only($data, ['instructor_id','title','description']));

        if (!empty($data['lessons'])) {
            $course->lessons()->createMany($data['lessons']);
        }

        $course->load(['instructor','lessons']);

        return new CourseResource($course);
    }

    public function show(Course $course)
    {
        $course->load(['instructor','lessons']);

        return new CourseResource($course);
    }

    public function update(CourseUpdateRequest $request, Course $course)
    {
        $data = $request->validated();
        $course->update(Arr::only($data, ['title','description']));

        if (array_key_exists('lessons', $data)) {
            $course->lessons()->delete();
            if (!empty($data['lessons'])) {
                $course->lessons()->createMany($data['lessons']);
            }
        }

        $course->load(['instructor','lessons']);

        return new CourseResource($course);
    }

    public function delete(Course $course)
    {
        $course->delete();
        return response()->noContent();
    }
}
