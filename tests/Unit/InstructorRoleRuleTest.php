<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use App\Rules\InstructorRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstructorRoleRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_passes_for_instructor(): void
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

        $rule = new InstructorRole();

        $this->assertTrue($rule->passes('instructor_id', $instructor->id));
    }

    public function test_fails_for_non_instructor(): void
    {
        $role = Role::where('slug', 'student')->firstOrCreate(
            ['slug' => 'student'],
            ['name' => 'Student']
        );
        $student = User::create([
            'name' => 'Student 1',
            'email' => 'Student1@example.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id
        ]);

        $rule = new InstructorRole();

        $this->assertFalse($rule->passes('student_id', $student->id));
    }

    public function test_fails_for_missing_user(): void
    {
        $rule = new InstructorRole();
        $this->assertFalse($rule->passes('instructor_id', 9999));
    }
}
