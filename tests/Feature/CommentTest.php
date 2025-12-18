<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_comments(): void
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

        $payload = ['text'=>'Curso de prueba','rating'=>4.5];
        $res = $this->postJson("/api/courses/{$course->id}/comments", $payload);
        $res->assertStatus(201)->assertJsonFragment(['text'=>'Curso de prueba']);

        $list = $this->getJson("/api/courses/{$course->id}/comments");
        $list->assertStatus(200)->assertJsonFragment(['text'=>'Curso de prueba']);
    }
}
