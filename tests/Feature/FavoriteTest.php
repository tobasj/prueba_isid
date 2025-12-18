<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_mark_and_unmark_favorite(): void
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

        $this->actingAs($student, 'api');

        $res = $this->postJson("/api/courses/{$course->id}/favorite");
        $res->assertNoContent();
        $this->assertDatabaseHas('favorites', ['user_id'=>$student->id,'course_id'=>$course->id]);

        $del = $this->deleteJson("/api/courses/{$course->id}/favorite");
        $del->assertNoContent();
        $this->assertSoftDeleted('favorites', ['user_id'=>$student->id,'course_id'=>$course->id]);
    }
}
