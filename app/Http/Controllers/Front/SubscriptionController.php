<?php

namespace App\Http\Controllers\Front;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Enums\SubscriptionTypeEnum;
use App\Http\Controllers\Controller;
use App\Exceptions\NoActiveStripeSubscriptionException;
use App\Services\SubscriptionChangeService;

class SubscriptionController extends Controller
{
    protected SubscriptionChangeService $changes;

    public function __construct(SubscriptionChangeService $changes)
    {
        $this->changes = $changes;
    }

    /**
     * Upgrade (immediate, prorated) or downgrade (scheduled at period end)
     * the user's existing subscription to another plan.
     */
    public function change(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|integer|exists:plans,id',
            'subscription_type' => 'required|string|in:' . implode(',', SubscriptionTypeEnum::getValues()),
        ]);

        $plan = Plan::where('is_active', true)->find($validated['plan_id']);

        if (! $plan) {
            return redirect()->back()->with('error', 'That plan is not available.');
        }

        try {
            $result = $this->changes->changePlan(auth()->user(), $plan, $validated['subscription_type']);
        } catch (NoActiveStripeSubscriptionException $e) {
            // No recurring Stripe subscription yet (e.g. paid by Direct Debit).
            // Send them through the normal purchase flow so their next payment
            // creates a Stripe subscription they can then upgrade/downgrade.
            return redirect()->route('virtual-address.show', $plan->slug)
                ->with('info', 'Please complete checkout to start this plan. Once your subscription is active you can upgrade or downgrade it from here.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        if (! empty($result['redirect_url'])) {
            return redirect()->away($result['redirect_url']);
        }

        if ($result['type'] === 'downgrade') {
            $subscription = auth()->user()->subscription('default');
            $when = $subscription?->scheduled_change_at?->format('F j, Y') ?? 'the end of your billing period';

            return redirect()->route('dashboard')->with('success', "Your plan will change to {$plan->name} on {$when}.");
        }

        if (! empty($result['order'])) {
            return redirect()->route('checkout.success', $result['order']->order_no)
                ->with('success', 'Your plan has been upgraded successfully.');
        }

        return redirect()->route('dashboard')->with('success', 'Your plan has been upgraded successfully.');
    }

    /**
     * Cancel the subscription at the end of the current billing period.
     */
    public function cancel(Request $request)
    {
        try {
            $subscription = $this->changes->cancel(auth()->user());
        } catch (NoActiveStripeSubscriptionException $e) {
            return redirect()->route('dashboard')->with('error', 'You do not have an active subscription to cancel.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $when = $subscription->ends_at?->format('F j, Y') ?? 'the end of your billing period';

        return redirect()->route('dashboard')->with('success', "Your subscription has been cancelled. It stays active until {$when}.");
    }

    /**
     * Resume a subscription within its grace period.
     */
    public function resume(Request $request)
    {
        if ($this->changes->resume(auth()->user())) {
            return redirect()->route('dashboard')->with('success', 'Your subscription has been resumed.');
        }

        return redirect()->route('dashboard')->with('error', 'Your subscription cannot be resumed.');
    }
}
