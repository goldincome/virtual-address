<?php
namespace App\Services;

use App\Models\Plan;
use App\Models\User;

class SubscriptionService
{

    /**
     * Get the current subscription plan for the user.
     *
     * @param User $user
     * @return Plan|null
     */
    public function getCurrentPlan(User $user): ?Plan
    {
        $subscription = $user->subscription('default');
        $plan = null;
        // Check if the subscription exists and is active
        if ($subscription && $subscription->active()) {
            // Resolve the plan from any of the subscription's Stripe price IDs
            $plan = $subscription->plan;
        }
        return $plan;
    }

    /**
     * Check if the user has an active subscription.
     *
     * @param User $user
     * @return bool
     */
    public function getActiveSubscription(User $user): bool
    {
        return $user->subscribed('default');
    }


    public function getSubscriptionEndDate(User $user): ?\Carbon\Carbon
    {
        if ($user->subscribed('default')) {
            return $user->subscription('default')->ends_at;
        }
        return null;
    }

   
}