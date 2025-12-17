<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingAggregationTest extends TestCase
{
    use RefreshDatabase;

    public function test_with_avg_and_count_comments(): void
    {
        $role = Role::where('slug', 'instructor')->firstOrCreate(
            ['slug' => 'instructor'],
            ['name' => 'Instructor']
        );
        $role2 = Role::where('slug', 'student')->firstOrCreate(
            ['slug' => 'student'],
            ['name' => 'Student']
        );
        $instructor = User::create([
            'name' => 'Instructor 1',
            'email' => 'Instructor1@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Title 1',
        ]);

        $student = User::create([
            'name' => 'Student 1',
            'email' => 'Student1@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role2->id
        ]);
        Comment::create(['course_id' => $course->id, 'user_id' => $student->id, 'rating' => 4.5]);
        Comment::create(['course_id' => $course->id, 'user_id' => $student->id, 'rating' => 3.5]);

        $courseWithAgg = Course::withAvg('comments', 'rating')
            ->withCount('comments')
            ->find($course->id);

        $this->assertEquals(4.0, round($courseWithAgg->comments_avg_rating, 1));
        $this->assertEquals(2, $courseWithAgg->comments_count);
    }
}
