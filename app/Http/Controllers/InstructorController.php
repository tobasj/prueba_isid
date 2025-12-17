<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;

class InstructorController extends Controller
{
    public function index()
    {
        $instructors = User::select('id','name')
            ->whereHas('role', fn($q) => $q->where('slug','instructor'))->cursorPaginate(500);

        return UserResource::collection($instructors);
    }
}
