<?php

namespace App\Http\Controllers;

use App\Http\Requests\SamosvalProblemsRequest;
use App\Models\SamosvalProblems;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SamosvalProblemsController extends Controller
{
    public function index(): JsonResponse
    {
        $problems = SamosvalProblems::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'problems' => $problems,
        ]);
    }

    public function store(SamosvalProblemsRequest $request): JsonResponse
    {
        $validated = $request->validated();

        SamosvalProblems::create($validated);

        $problems = SamosvalProblems::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Поломка создана',
            'problems' => $problems,
        ]);
    }

    public function update(SamosvalProblemsRequest $request, SamosvalProblems $problem): JsonResponse
    {
        $validated = $request->validated();
        $problem->update($validated);

        $problems = SamosvalProblems::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Поломка обновлена',
            'problems' => $problems,
        ]);
    }
}
