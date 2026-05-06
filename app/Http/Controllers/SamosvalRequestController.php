<?php

namespace App\Http\Controllers;

use App\Models\SamosvalRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SamosvalRequestController extends Controller
{
    public function index(): JsonResponse
    {
        $requests = SamosvalRequest::with(['samosval', 'problem', 'solution'])
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'status' => true,
            'requests' => $requests,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'samosval_id' => 'required|exists:samosvals,id',
            'problem_id' => 'required|exists:samosval_problems,id',
        ]);

        $data['status'] = SamosvalRequest::STATUS_OPEN;
        $data['solution_id'] = null;

        SamosvalRequest::create($data);

        return $this->index();
    }

    public function update(Request $request, SamosvalRequest $samosvalRequest): JsonResponse
    {
        if ($samosvalRequest->status !== SamosvalRequest::STATUS_OPEN) {
            throw ValidationException::withMessages([
                'status' => ['Редактировать можно только новые заявки.']
            ]);
        }

        $validated = $request->validate([
            'samosval_id' => 'required|exists:samosvals,id',
            'problem_id' => 'required|exists:samosval_problems,id',
        ]);

        $samosvalRequest->update($validated);

        return $this->index();
    }

    public function takeInWork(SamosvalRequest $samosvalRequest): JsonResponse
    {
        if (!in_array($samosvalRequest->status, [SamosvalRequest::STATUS_OPEN, SamosvalRequest::STATUS_WAITING_PARTS], true)) {
            throw ValidationException::withMessages([
                'status' => ['Перевести в работу можно только новую заявку или заявку в ожидании запчастей.']
            ]);
        }

        $samosvalRequest->update(['status' => SamosvalRequest::STATUS_IN_WORK]);

        return $this->index();
    }

    public function close(Request $request, SamosvalRequest $samosvalRequest): JsonResponse
    {
        $data = $request->validate([
            'solution_id' => 'required|exists:samosval_solutions,id',
        ]);

        $samosvalRequest->update([
            'status' => SamosvalRequest::STATUS_COMPLETED,
            'solution_id' => $data['solution_id']
        ]);

        return $this->index();
    }

    public function cancel(SamosvalRequest $samosvalRequest): JsonResponse
    {
        $samosvalRequest->update(['status' => SamosvalRequest::STATUS_WAITING_PARTS]);

        return $this->index();
    }
}
