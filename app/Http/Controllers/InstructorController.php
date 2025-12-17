<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use OpenApi\Annotations as OA;

class InstructorController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/instructors",
     *   summary="Listar instructores",
     *   tags={"Instructors"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="cursor", in="query", description="Cursor para paginación", @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="Listado de instructores")
     * )
     */
    public function index()
    {
        $instructors = User::select('id','name')
            ->whereHas('role', fn($q) => $q->where('slug','instructor'))->cursorPaginate(500);

        return UserResource::collection($instructors);
    }
}
