<?php

namespace App\Application\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUser
{
    public function __construct(private IssuePasswordToken $issuePasswordToken) {

    }

    public function handle(string $name, string $email, string $password, string $role, bool $withToken = true): array
    {
        $role_id = Role::where('slug', $role)->first()->id ?? null;
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $role_id,
        ]);

        if (!$withToken) {
            return ['id' => $user->id];
        }

        return $this->issuePasswordToken->forCredentials($email, $password);
    }
}
