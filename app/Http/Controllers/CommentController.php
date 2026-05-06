<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Comment;
use App\Models\Lead;

class CommentController extends Controller
{
    public function store(Request $request, $leadId): JsonResponse
    {
        $lead = Lead::findOrFail($leadId);

        $data = $request->validate([
            'body' => 'required|string|min:1',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $comment = Comment::create([
            'lead_id' => $leadId,
            'user_id' => $data['user_id'],
            'body' => $data['body'],
        ]);

        return response()->json([
            'data' => [
                'id' => $comment->id,
                'body' => $comment->body,
                'user' => ['id' => $comment->user->id, 'name' => $comment->user->name, 'role' => $comment->user->role],
                'created_at' => $comment->created_at,
            ],
        ], 201);
    }

    public function index($leadId): JsonResponse
    {
        $lead = Lead::findOrFail($leadId);
        $comments = $lead->comments()->with('user')->get();

        return response()->json([
            'data' => $comments->map(fn($c) => [
                'id' => $c->id,
                'body' => $c->body,
                'user' => ['id' => $c->user->id, 'name' => $c->user->name, 'role' => $c->user->role],
                'created_at' => $c->created_at,
            ]),
        ]);
    }
}
