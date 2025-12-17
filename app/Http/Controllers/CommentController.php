<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comments\CommentStoreRequest;
use App\Http\Resources\CommentResource;
use App\Models\Course;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CommentController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/courses/{course}/comments",
     *   summary="Listar comentarios de un curso",
     *   tags={"Comments"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Listado paginado de comentarios")
     * )
     */
    public function index(Course $course)
    {
        $comments = $course->comments()->with('user')->latest()->paginate(15);
        return CommentResource::collection($comments);
    }

    /**
     * @OA\Post(
     *   path="/api/courses/{course}/comments",
     *   summary="Crear comentario de un curso",
     *   tags={"Comments"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"text","rating"},
     *       @OA\Property(property="text", type="string"),
     *       @OA\Property(property="rating", type="number", format="float", minimum=1, maximum=5)
     *     )
     *   ),
     *   @OA\Response(response=200, description="Comentario creado"),
     *   @OA\Response(response=422, description="Errores de validación")
     * )
     */
    public function store(CommentStoreRequest $request, Course $course)
    {
        $data = $request->validated();

        $comment = $course->comments()->create([
            'user_id' => $request->user()->id,
            'text' => $data['text'],
            'rating' => $data['rating'],
        ])->load('user');

        return new CommentResource($comment);
    }
}
