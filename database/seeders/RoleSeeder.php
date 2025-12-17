<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(array(
            'slug' => 'instructor',
            'name' => 'instructor',
        ));

        Role::create(array(
            'slug' => 'student',
            'name' => 'Student',
        ));
    }
}
