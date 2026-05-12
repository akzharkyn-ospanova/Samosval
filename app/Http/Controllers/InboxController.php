<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Lead;

class InboxController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        // Get user_id from query params
        $userId = $request->query('user_id');
        $userRole = $request->query('role');

        $query = Lead::orderBy('created_at', 'desc');

        // Sales Manager and Super Admin see all leads
        // No filtering - all users see all leads

        $items = $query->get();
        $total = $items->count();

        $list = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'contacts' => $item->contacts,
                'comment' => $item->comment,
                'source' => $item->source,
                'assigned_to' => $item->assigned_to,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return response()->json([
            'data' => $list,
            'analytics' => [
                'total' => $total,
            ],
        ]);
    }

    public function history($id): JsonResponse
    {
        // Placeholder page: feature in development
        return response()->json([
            'id' => $id,
            'text' => 'Данный раздел еще в создании',
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'contacts' => 'required|string|min:3|max:255',
            'comment' => 'required|string|min:5',
            'source' => 'required|string|min:2|max:255',
            'user_id' => 'sometimes|integer', // ignore user_id from request
        ], [
            'name.required' => 'Заполните имя клиента.',
            'name.min' => 'Имя должно содержать не менее 2 символов.',
            'contacts.required' => 'Укажите контакты клиента.',
            'contacts.min' => 'Контакты должны содержать не менее 3 символов.',
            'comment.required' => 'Укажите комментарий к заявке.',
            'comment.min' => 'Комментарий должен содержать не менее 5 символов.',
            'source.required' => 'Укажите источник заявки.',
            'source.min' => 'Источник должен содержать не менее 2 символов.',
        ]);
        
        // Remove user_id from data before creating
        unset($data['user_id']);
        $lead = Lead::create($data);

        return response()->json(['data' => $lead], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $lead = Lead::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|min:2|max:255',
            'contacts' => 'sometimes|required|string|min:3|max:255',
            'comment' => 'sometimes|required|string|min:5',
            'source' => 'sometimes|required|string|min:2|max:255',
            'assigned_to' => 'sometimes|nullable|integer|exists:staff_members,id',
            'user_id' => 'sometimes|integer', // ignore user_id from request
        ]);
        
        // Remove user_id from data before updating
        unset($data['user_id']);
        $lead->update($data);

        return response()->json(['data' => $lead]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        // Validate and ignore user_id if present
        $request->validate([
            'user_id' => 'sometimes|integer',
        ]);
        
        $lead = Lead::findOrFail($id);
        $lead->delete();
        return response()->json(['deleted' => true]);
    }
}
