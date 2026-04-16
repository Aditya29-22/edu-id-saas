<?php

namespace App\Http\Middleware;

use App\Models\School;
use App\Models\Subscription;
use Closure;
use Illuminate\Http\Request;

class SubscriptionCheckMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if ($user->role === 'super_admin') {
            return $next($request);
        }

        $schoolId = $user->school_id;
        if (!$schoolId) {
            return response()->json([
                'success' => false,
                'message' => 'No school associated.'
            ], 403);
        }

        $school = School::find($schoolId);
        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'School not found.'
            ], 404);
        }

        if (in_array($school->subscription_status, ['expired', 'none'])) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription expired. Please renew to access this feature.',
                'subscription_expired' => true
            ], 403);
        }

        $activeSubscription = Subscription::where('school_id', $schoolId)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->first();

        if (!$activeSubscription) {
            $school->update(['subscription_status' => 'expired']);

            return response()->json([
                'success' => false,
                'message' => 'Subscription expired. Please renew.',
                'subscription_expired' => true
            ], 403);
        }

        $request->merge(['active_subscription' => $activeSubscription]);

        return $next($request);
    }
}
