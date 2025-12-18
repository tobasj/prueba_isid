<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RatingTest extends TestCase
{
    use RefreshDatabase;
    
    public function testRating(): void
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
        $this->postJson("/api/courses/{$course->id}/comments", ['text'=>'Mu buen cursillo','rating'=>4.0])->assertStatus(201);

        $course->refresh();
        $this->assertEquals(4.0, $course->average_rating);
        $this->assertEquals(1, $course->ratings_count);

        $this->postJson("/api/courses/{$course->id}/comments", ['text'=>'Una caca','rating'=>2.0])->assertStatus(201);
        $course->refresh();
        $this->assertEquals(3.0, $course->average_rating);
        $this->assertEquals(2, $course->ratings_count);
    }
}
