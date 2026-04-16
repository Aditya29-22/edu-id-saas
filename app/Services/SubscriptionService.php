<?php

namespace App\Services;

use App\Models\School;
use App\Models\Subscription;

class SubscriptionService
{
    public function checkExpiredSubscriptions(): int
    {
        $expired = Subscription::where('status', 'active')
            ->where('end_date', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $subscription) {
            $subscription->update(['status' => 'expired']);

            $hasActive = Subscription::where('school_id', $subscription->school_id)
                ->where('status', 'active')
                ->where('end_date', '>=', now())
                ->exists();

            if (!$hasActive) {
                School::where('id', $subscription->school_id)->update([
                    'subscription_status' => 'expired'
                ]);
            }

            $count++;
        }

        return $count;
    }
}
