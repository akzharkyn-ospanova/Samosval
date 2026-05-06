<?php

namespace App\Http\Controllers;

use App\Http\Requests\SamosvalRequest;
use App\Models\Samosval;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SamosvalController extends Controller
{
    public function index(): JsonResponse {
        return $this->getResponse();
    }

    public function store(SamosvalRequest $request): JsonResponse
    {
        $validated = $request->validated();
        Samosval::create($validated);

        return $this->getResponse();
    }

    public function update(SamosvalRequest $request, Samosval $samosval): JsonResponse
    {
        $validated = $request->validated();
        $samosval->update($validated);

        return $this->getResponse();
    }

    private function getResponse()
    {
        $Samosvals = Samosval::all();

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'Samosvals' => $Samosvals,
        ]);
    }

}
