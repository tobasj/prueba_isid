<?php

namespace App\Http\Controllers;

use App\Application\Auth\IssuePasswordToken;
use App\Application\Auth\RegisterUser;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="API Cursos",
 *     version="1.0.0"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/register",
     *   summary="Registro de usuario",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"name","email","password"},
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="email", type="string", format="email"),
     *       @OA\Property(property="password", type="string", format="password"),
     *       @OA\Property(property="role", type="string", example="student")
     *     )
     *   ),
     *   @OA\Response(response=201, description="Usuario creado y token emitido"),
     *   @OA\Response(response=422, description="Errores de validación")
     * )
     */
    public function register(RegisterRequest $request, RegisterUser $registerUser)
    {
        $data = $request->validated();
        return response()->json(
            $registerUser->handle($data['name'], $data['email'], $data['password'], $data['role'] ?? 'student', true),
            201
        );
    }

    /**
     * @OA\Post(
     *   path="/api/login",
     *   summary="Login con password grant",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email"),
     *       @OA\Property(property="password", type="string", format="password")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Token emitido"),
     *   @OA\Response(response=401, description="Credenciales inválidas")
     * )
     */
    public function login(LoginRequest $request, IssuePasswordToken $issueToken)
    {
        $data = $request->validated();
        return response()->json(
            $issueToken->forCredentials($data['email'], $data['password'])
        );
    }
}
