<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseSoftDeletesTest extends TestCase
{
    use RefreshDatabase;

    public function test_soft_delete_and_restore(): void
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

        $id = $course->id;
        $course->delete();
        $this->assertSoftDeleted('courses', ['id' => $id]);

        $course->restore();
        $this->assertDatabaseHas('courses', ['id' => $course->id, 'deleted_at' => null]);
    }
}
