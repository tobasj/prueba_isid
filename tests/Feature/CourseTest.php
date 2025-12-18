<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_course_with_lessons(): void
    {
        $role = Role::where('slug', 'instructor')->firstOrCreate(
            ['slug' => 'instructor'],
            ['name' => 'Instructor']
        );
        $instructor = User::create([
            'name' => 'Instructor 1',
            'email' => 'Instructor1@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id
        ]);

        $this->actingAs($instructor, 'api');

        $payload = [
            'instructor_id' => $instructor->id,
            'title' => 'Curso API',
            'description' => 'Desc',
            'lessons' => [
                ['title'=>'L1','video_url'=>'https://example.com/v1','order'=>1],
            ],
        ];

        $res = $this->postJson('/api/courses', $payload);

        $res->assertStatus(201)->assertJsonFragment(['title' => 'Curso API']);

        $this->assertDatabaseHas('courses', ['title' => 'Curso API']);
        $this->assertDatabaseHas('lessons', ['title' => 'L1']);
    }

    public function test_list_courses(): void
    {
        $role = Role::where('slug', 'instructor')->firstOrCreate(
            ['slug' => 'instructor'],
            ['name' => 'Instructor']
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
        $course2 = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Title 2',
        ]);

        $this->actingAs($instructor, 'api');

        $res = $this->getJson('/api/courses');

        $res->assertStatus(200)->assertJsonFragment(['title' => 'Title 1'])->assertJsonFragment(['title' => 'Title 2']);
    }

    public function test_show_course(): void
    {
        $role = Role::where('slug', 'instructor')->firstOrCreate(
            ['slug' => 'instructor'],
            ['name' => 'Instructor']
        );
        $instructor = User::create([
            'name' => 'Instructor 1',
            'email' => 'Instructor1@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Title 3',
        ]);
        $this->actingAs($instructor, 'api');

        $res = $this->getJson("/api/courses/{$course->id}");

        $res->assertStatus(200)->assertJsonFragment(['title' => 'Title 3']);
    }


    public function test_delete_course(): void
    {
        $role = Role::where('slug', 'instructor')->firstOrCreate(
            ['slug' => 'instructor'],
            ['name' => 'Instructor']
        );
        $instructor = User::create([
            'name' => 'Instructor 1',
            'email' => 'Instructor1@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id
        ]);
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Title 4',
        ]);

        $this->actingAs($instructor, 'api');

        $res = $this->deleteJson("/api/courses/{$course->id}");

        $res->assertNoContent();
        $this->assertSoftDeleted('courses', ['id'=>$course->id]);
    }
}
