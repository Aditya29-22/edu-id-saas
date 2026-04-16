<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(): JsonResponse
    {
        $plans = Plan::where('is_active', true)->orderBy('price_monthly')->get();
        return response()->json(['success' => true, 'data' => $plans]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:plans,code',
            'description' => 'nullable|string',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'max_students' => 'required|integer|min:1',
            'max_users' => 'required|integer|min:1',
            'storage_gb' => 'required|integer|min:1',
            'custom_templates' => 'boolean',
            'analytics_access' => 'boolean',
            'api_access' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $plan = Plan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Plan created.',
            'data' => $plan
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'price_monthly' => 'sometimes|numeric|min:0',
            'price_yearly' => 'sometimes|numeric|min:0',
            'max_students' => 'sometimes|integer|min:1',
            'max_users' => 'sometimes|integer|min:1',
            'storage_gb' => 'sometimes|integer|min:1',
            'custom_templates' => 'boolean',
            'analytics_access' => 'boolean',
            'api_access' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $plan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Plan updated.',
            'data' => $plan->fresh()
        ]);
    }
}
