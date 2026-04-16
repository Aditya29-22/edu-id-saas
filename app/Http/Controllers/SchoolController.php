<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index(): JsonResponse
    {
        $schools = School::with('activeSubscription')
                        ->when(request('search'), function ($q) {
                            $q->where('name', 'LIKE', '%' . request('search') . '%')
                              ->orWhere('code', 'LIKE', '%' . request('search') . '%');
                        })
                        ->orderBy('created_at', 'desc')
                        ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $schools
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'code' => 'required|string|max:20|unique:schools,code',
            'email' => 'required|email|unique:schools,email',
            'phone' => 'required|string|max:15',
            'street' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'entry_time' => 'nullable|date_format:H:i',
            'late_threshold' => 'nullable|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $school = School::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'School created successfully.',
            'data' => $school
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $school = School::with(['activeSubscription', 'activeTemplate'])
                       ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $school
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $school = School::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:200',
            'email' => 'sometimes|email|unique:schools,email,' . $id,
            'phone' => 'sometimes|string|max:15',
            'street' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'entry_time' => 'nullable|date_format:H:i',
            'late_threshold' => 'nullable|date_format:H:i',
            'exit_time' => 'nullable|date_format:H:i',
            'active_template_id' => 'nullable|exists:templates,id',
        ]);

        $school->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'School updated successfully.',
            'data' => $school->fresh()
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $school = School::findOrFail($id);
        $school->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'School deactivated successfully.'
        ]);
    }
}
