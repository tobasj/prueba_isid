<?php

namespace App\Application\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RegisterUser
{
    public function __construct(private IssuePasswordToken $issuePasswordToken) {

    }

    public function handle(string $name, string $email, string $password, bool $withToken = true): array
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        if (! $withToken) {
            return ['id' => $user->id];
        }

        return $this->issuePasswordToken->forCredentials($email, $password);
    }
}
