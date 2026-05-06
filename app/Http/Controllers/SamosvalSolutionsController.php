<?php

namespace App\Http\Controllers;

use App\Http\Requests\SamosvalSolutionsRequest;
use App\Models\SamosvalSolutions;
use Illuminate\Http\JsonResponse;

class SamosvalSolutionsController extends Controller
{
    public function index(): JsonResponse
    {
        $solutions = SamosvalSolutions::with('problem')->get();

        return response()->json([
            'status' => true,
            'solutions' => $solutions
        ]);
    }

    public function store(SamosvalSolutionsRequest $request): JsonResponse
    {
        $solution = SamosvalSolutions::create($request->validated());
        return response()->json($solution->load('problem'), 201);
    }

    public function update(SamosvalSolutionsRequest $request, SamosvalSolutions $solution): JsonResponse
    {
        $solution->update($request->validated());
        return response()->json($solution->load('problem'));
    }
}
