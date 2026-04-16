<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::with('school')
            ->when(request('role'), fn($q) => $q->where('role', request('role')))
            ->when(request('school_id'), fn($q) => $q->where('school_id', request('school_id')))
            ->when(request('search'), function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'LIKE', '%' . request('search') . '%')
                          ->orWhere('email', 'LIKE', '%' . request('search') . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json(['success' => true, 'data' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:super_admin,school_admin,teacher,student,security_guard',
            'school_id' => 'required_unless:role,super_admin|exists:schools,id',
            'phone' => 'nullable|string|max:15',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        if ($validated['role'] === 'super_admin' && !auth()->user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Only super admin can create super admin users.'
            ], 403);
        }

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data' => $user
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = User::with('school')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $user]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|in:school_admin,teacher,student,security_guard',
            'school_id' => 'sometimes|exists:schools,id',
            'phone' => 'nullable|string|max:15',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $user->fresh()
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete your own account.'
            ], 400);
        }

        $user->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'User deactivated.'
        ]);
    }
}
