<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class FavoriteController extends Controller
{
    /**
     * @OA\Post(
     *   path="/api/courses/{course}/favorite",
     *   summary="Marcar curso como favorito",
     *   tags={"Favorites"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=204, description="Marcado como favorito"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function store(Request $request, Course $course)
    {
        $request->user()->favorites()->firstOrCreate(['course_id' => $course->id]);
        return response()->noContent();
    }

    /**
     * @OA\Delete(
     *   path="/api/courses/{course}/favorite",
     *   summary="Quitar curso de favoritos",
     *   tags={"Favorites"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=204, description="Favorito eliminado"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function destroy(Request $request, Course $course)
    {
        $request->user()->favorites()->where('course_id', $course->id)->delete();
        return response()->noContent();
    }
}
