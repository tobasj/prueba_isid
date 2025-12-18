<?php

namespace App\Http\Controllers;

use App\Http\Requests\Courses\CourseStoreRequest;
use App\Http\Requests\Courses\CourseUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use OpenApi\Annotations as OA;

class CourseController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/courses",
     *   summary="Listar cursos",
     *   tags={"Courses"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="page", in="query", @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Listado paginado de cursos")
     * )
     */
    public function index()
    {
        $courses = Course::with(['instructor','lessons'])->paginate(15);

        return CourseResource::collection($courses);
    }

    /**
     * @OA\Post(
     *   path="/api/courses",
     *   summary="Crear curso",
     *   tags={"Courses"},
     *   security={{"bearerAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"instructor_id","title"},
     *       @OA\Property(property="instructor_id", type="integer"),
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="description", type="string"),
     *       @OA\Property(
     *         property="lessons",
     *         type="array",
     *         @OA\Items(
     *           @OA\Property(property="title", type="string"),
     *           @OA\Property(property="video_url", type="string"),
     *           @OA\Property(property="order", type="integer")
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(response=200, description="Curso creado"),
     *   @OA\Response(response=422, description="Errores de validación")
     * )
     */
    public function store(CourseStoreRequest $request)
    {
        $data = $request->validated();

        $course = Course::create(Arr::only($data, ['instructor_id','title','description']));

        if (!empty($data['lessons'])) {
            $course->lessons()->createMany($data['lessons']);
        }

        $course->load(['instructor','lessons']);

        return new CourseResource($course);
    }

    /**
     * @OA\Get(
     *   path="/api/courses/{course}",
     *   summary="Detalle de curso",
     *   tags={"Courses"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=200, description="Curso"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function show(Course $course)
    {
        $course->load(['instructor','lessons']);

        return new CourseResource($course);
    }

    /**
     * @OA\Put(
     *   path="/api/courses/{course}",
     *   summary="Actualizar curso",
     *   tags={"Courses"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="description", type="string"),
     *       @OA\Property(
     *         property="lessons",
     *         type="array",
     *         @OA\Items(
     *           @OA\Property(property="title", type="string"),
     *           @OA\Property(property="video_url", type="string"),
     *           @OA\Property(property="order", type="integer")
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(response=200, description="Curso actualizado"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     * @OA\Patch(
     *   path="/api/courses/{course}",
     *   summary="Actualizar curso",
     *   tags={"Courses"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\RequestBody(
     *     required=false,
     *     @OA\JsonContent(
     *       @OA\Property(property="title", type="string"),
     *       @OA\Property(property="description", type="string"),
     *       @OA\Property(
     *         property="lessons",
     *         type="array",
     *         @OA\Items(
     *           @OA\Property(property="title", type="string"),
     *           @OA\Property(property="video_url", type="string"),
     *           @OA\Property(property="order", type="integer")
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(response=200, description="Curso actualizado"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function update(CourseUpdateRequest $request, Course $course)
    {
        $data = $request->validated();
        $course->update(Arr::only($data, ['title','description']));

        if (array_key_exists('lessons', $data)) {
            $course->lessons()->delete();
            if (!empty($data['lessons'])) {
                $course->lessons()->createMany($data['lessons']);
            }
        }

        $course->load(['instructor','lessons']);

        return new CourseResource($course);
    }

    /**
     * @OA\Delete(
     *   path="/api/courses/{course}",
     *   summary="Eliminar curso",
     *   tags={"Courses"},
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Response(response=204, description="Eliminado"),
     *   @OA\Response(response=404, description="No encontrado")
     * )
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return response()->noContent();
    }
}
