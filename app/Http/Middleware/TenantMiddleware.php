<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        if ($user->role === 'super_admin') {
            $tenantId = $request->route('schoolId')
                        ?? $request->query('school_id')
                        ?? $request->input('school_id');
            $request->merge(['tenant_id' => $tenantId]);
            return $next($request);
        }

        if (!$user->school_id) {
            return response()->json([
                'success' => false,
                'message' => 'No school associated with this user.'
            ], 403);
        }

        $request->merge(['tenant_id' => $user->school_id]);

        $requestSchoolId = $request->route('schoolId')
                          ?? $request->query('school_id')
                          ?? $request->input('school_id');

        if ($requestSchoolId && (int)$requestSchoolId !== (int)$user->school_id) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Cross-tenant access not allowed.'
            ], 403);
        }

        return $next($request);
    }
}
