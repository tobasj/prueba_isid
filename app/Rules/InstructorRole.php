<?php

namespace App\Rules;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class InstructorRole implements Rule
{
    public function passes($attribute, $value): bool
    {
        $user = User::find($value);
        return $user && $user->role?->slug == 'instructor';
    }

    public function message(): string
    {
        return 'User not instructor.';
    }
}
