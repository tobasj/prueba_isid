<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Favorite;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteUniquenessTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_or_create_prevents_duplicates(): void
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

        $student->favorites()->firstOrCreate(['course_id' => $course->id]);
        $student->favorites()->firstOrCreate(['course_id' => $course->id]);

        $this->assertEquals(1, Favorite::where('user_id', $student->id)->where('course_id', $course->id)->count());
    }
}
