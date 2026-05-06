<?php

namespace App\Http\Controllers;

use App\Models\StaffMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffMemberController extends Controller
{
    public function index(): JsonResponse
    {
        $members = StaffMember::orderBy('name')->get();

        return response()->json([
            'data' => $members,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'role' => 'required|in:manager,mechanic,admin',
            'contact' => 'required|string|regex:/^\+7[\s(]*\d{3}[\s)-]*\d{3}[\s-]*\d{2}[\s-]*\d{2}$/',
            'status' => 'required|in:online,offline,vacation',
        ], [
            'name.required' => 'Укажите имя сотрудника.',
            'name.min' => 'Имя должно содержать не менее 2 символов.',
            'role.required' => 'Укажите роль сотрудника.',
            'role.in' => 'Роль должна быть менеджер, механик или админ.',
            'contact.required' => 'Укажите контакт сотрудника.',
            'contact.regex' => 'Формат: +7 (999) 999-99-99 или +79999999999.',
            'status.required' => 'Укажите статус сотрудника.',
            'status.in' => 'Статус должен быть online, offline или vacation.',
        ]);

        $member = StaffMember::create($data);

        return response()->json([
            'data' => $member,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $member = StaffMember::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'role' => 'required|in:manager,mechanic,admin',
            'contact' => 'required|string|regex:/^\+7[\s(]*\d{3}[\s)-]*\d{3}[\s-]*\d{2}[\s-]*\d{2}$/',
            'status' => 'required|in:online,offline,vacation',
        ], [
            'name.required' => 'Укажите имя сотрудника.',
            'name.min' => 'Имя должно содержать не менее 2 символов.',
            'role.required' => 'Укажите роль сотрудника.',
            'role.in' => 'Роль должна быть менеджер, механик или админ.',
            'contact.required' => 'Укажите контакт сотрудника.',
            'contact.regex' => 'Формат: +7 (999) 999-99-99 или +79999999999.',
            'status.required' => 'Укажите статус сотрудника.',
            'status.in' => 'Статус должен быть online, offline или vacation.',
        ]);

        $member->update($data);

        return response()->json([
            'data' => $member,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $member = StaffMember::findOrFail($id);
        $member->delete();

        return response()->json(['deleted' => true]);
    }
}