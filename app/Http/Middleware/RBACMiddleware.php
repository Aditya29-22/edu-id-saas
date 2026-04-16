<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RBACMiddleware
{
    private array $permissions = [
        'super_admin' => [
            'manage_schools', 'manage_all_users', 'manage_plans',
            'view_all_data', 'manage_templates', 'view_payments'
        ],
        'school_admin' => [
            'manage_school_users', 'manage_students', 'view_attendance',
            'manage_school_templates', 'generate_id_cards', 'manage_subscription',
            'view_school_data'
        ],
        'teacher' => [
            'view_students', 'view_attendance', 'view_school_data'
        ],
        'student' => [
            'view_own_data', 'view_own_attendance', 'view_own_id_card'
        ],
        'security_guard' => [
            'scan_qr', 'mark_attendance', 'view_attendance'
        ],
    ];

    public function handle(Request $request, Closure $next, ...$requiredPermissions): mixed
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        $userPermissions = $this->permissions[$user->role] ?? [];

        $hasPermission = collect($requiredPermissions)->contains(function ($permission) use ($userPermissions) {
            return in_array($permission, $userPermissions);
        });

        if (!$hasPermission) {
            return response()->json([
                'success' => false,
                'message' => "Access denied. Role '{$user->role}' lacks required permissions."
            ], 403);
        }

        return $next($request);
    }
}
