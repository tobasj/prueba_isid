<?php

namespace App\Http\Controllers;

use App\Application\Auth\IssuePasswordToken;
use App\Application\Auth\RegisterUser;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUser $registerUser)
    {
        $data = $request->validated();
        return response()->json(
            $registerUser->handle($data['name'], $data['email'], $data['password'], $data['role'] ?? 'student', true),
            201
        );
    }

    public function login(LoginRequest $request, IssuePasswordToken $issueToken)
    {
        $data = $request->validated();
        return response()->json(
            $issueToken->forCredentials($data['email'], $data['password'])
        );
    }
}
