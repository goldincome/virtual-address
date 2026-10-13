<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\PlanSubscription;
use App\Models\PscSubscriptionItem;
use App\Enums\SubscriptionTypeEnum;
use App\Jobs\SendSubscriptionCancelledEmail;
use App\Exceptions\NoActiveStripeSubscriptionException;

/**
 * Central place for changing, cancelling and resuming a user's single
 * "default" Stripe subscription.
 *
 *    - Upgrade:   applied immediately, in-place, with credit proration.
 *    - Downgrade: scheduled with a Stripe subscription schedule, so Stripe
 *                 switches the plan exactly at period end (no extra charge).
 *    - Cancel:    at period end (grace period), emails on request and on end.
 *    - Resume:    undo a cancellation within the grace period.
 *
 * Feature access everywhere in the app is derived from
 * `$user->subscription('default')->plan`, so hiding/showing features follows
 * the subscription automatically.
 */
class SubscriptionChangeService
{
    /**
     * Apply a plan change for an existing subscriber.
     *
     * @return array{type: string, order: \App\Models\Order|null, redirect_url: string|null}
     */
    public function changePlan(User $user, Plan $newPlan, string $interval): array
    {
        $subscription = $this->requireActiveSubscription($user);
        $currentPlan = $subscription->plan;

        if (! $currentPlan) {
            throw new \RuntimeException('We could not determine your current plan. Please contact support.');
        }

        if ($currentPlan->id === $newPlan->id) {
            throw new \RuntimeException('You are already subscribed to this plan.');
        }

        // Drop any previously scheduled change before applying the new one.
        // This also releases a schedule that exists on Stripe but is missing
        // locally (e.g. a previous attempt that failed before it was saved),
        // otherwise `from_subscription` would be rejected by Stripe.
        $this->cancelScheduledChange($subscription);
        $subscription->refresh();

        if ((int) $newPlan->level > (int) $currentPlan->level) {
            return $this->upgrade($user, $subscription, $newPlan, $interval);
        }

        $this->scheduleDowngrade($subscription, $newPlan, $interval);

        return ['type' => 'downgrade', 'order' => null, 'redirect_url' => null];
    }

    /**
     * Apply an upgrade immediately with credit proration. Returns the Stripe
     * hosted invoice URL when there is an amount left to collect (so the card
     * details are always handled by Stripe — the app stays PCI compliant).
     *
     * @return array{type: string, order: \App\Models\Order|null, redirect_url: string|null}
     */
    protected function upgrade(User $user, PlanSubscription $subscription, Plan $newPlan, string $interval): array
    {
        $currentPlan = $subscription->plan;
        $planItem = $this->findPlanItem($subscription, $currentPlan);

        if (! $planItem) {
            throw new \RuntimeException('We could not locate your current plan on Stripe. Please contact support.');
        }

        $newPriceId = $this->priceIdForInterval($newPlan, $interval);
        if (! $newPriceId) {
            throw new \RuntimeException('Stripe pricing is not configured for this plan yet.');
        }

        $order = app(OrderService::class)->createPlanChangeOrder($user, $newPlan, $interval);
        $orderNo = $order->order_no;

        $stripe = $user->stripe();

        // Change ONLY the plan item; PSC and metered mail items are preserved.
        // `always_invoice` generates the prorated invoice now (credit for the
        // unused part of the current period + charge for the new plan).
        $stripeSubscription = $stripe->subscriptions->update($subscription->stripe_id, [
            'items' => [[
                'id' => $planItem->stripe_id,
                'price' => $newPriceId,
                'quantity' => 1,
            ]],
            'proration_behavior' => 'always_invoice',
            'payment_behavior' => 'allow_incomplete',
            'expand' => ['latest_invoice'],
            'metadata' => ['order_no' => $orderNo],
        ]);

        $invoice = $stripeSubscription->latest_invoice;
        if ($invoice) {
            try {
                $stripe->invoices->update($invoice->id, ['metadata' => ['order_no' => $orderNo]]);
            } catch (\Exception $e) {
                report($e);
            }
        }

        // Reflect the new plan locally straight away so the new features are
        // available before the Stripe webhook round-trip completes.
        $planItem->stripe_price = $newPriceId;
        $planItem->save();
        $subscription->unsetRelation('items');
        $subscription->refresh();

        if ($invoice && $invoice->status === 'paid') {
            app(OrderService::class)->confirmPayment($order, $invoice->id);

            return ['type' => 'upgrade', 'order' => $order, 'redirect_url' => null];
        }

        if (! $invoice || (float) ($invoice->amount_due ?? 0) <= 0) {
            // Nothing left to collect (e.g. a pure credit); treat as settled.
            app(OrderService::class)->confirmPayment($order, $invoice->id ?? ('order-' . $order->id));

            return ['type' => 'upgrade', 'order' => $order, 'redirect_url' => null];
        }

        return [
            'type' => 'upgrade',
            'order' => $order,
            'redirect_url' => $invoice->hosted_invoice_url ?? null,
        ];
    }

    /**
     * Schedule a downgrade using a Stripe subscription schedule. Phase 1 keeps
     * the current plan until period end; phase 2 switches to the new plan.
     */
    protected function scheduleDowngrade(PlanSubscription $subscription, Plan $newPlan, string $interval): PlanSubscription
    {
        $newPriceId = $this->priceIdForInterval($newPlan, $interval);
        if (! $newPriceId) {
            throw new \RuntimeException('Stripe pricing is not configured for this plan yet.');
        }

        $stripe = $subscription->owner->stripe();

        $stripeSubscription = $stripe->subscriptions->retrieve($subscription->stripe_id, [
            'expand' => ['items.data.price'],
        ]);

        $currentPlan = $subscription->plan;
        $planPriceIds = array_filter([
            $currentPlan?->stripe_price_id_monthly,
            $currentPlan?->stripe_price_id_yearly,
        ]);

        // Metered mail prices the new plan is allowed to keep.
        $allowedMailPriceIds = $newPlan->mailSettings()
            ->where('status', true)
            ->pluck('stripe_price_id')
            ->filter()
            ->all();

        $pscItemIds = PscSubscriptionItem::where('user_id', $subscription->user_id)
            ->pluck('stripe_subscription_item_id')
            ->filter()
            ->all();

        $phaseOneItems = [];
        $phaseTwoItems = [];

        foreach ($stripeSubscription->items->data as $item) {
            $isPlanItem = in_array($item->price->id, $planPriceIds, true);
            $isPscItem = in_array($item->id, $pscItemIds, true);
            $isMetered = ($item->price->recurring->usage_type ?? 'licensed') === 'metered';

            // Phase 1 keeps everything exactly as it is today.
            $phaseOneItems[] = $this->toScheduleItem($item);

            if ($isPlanItem) {
                $phaseTwoItems[] = ['price' => $newPriceId, 'quantity' => 1];
                continue;
            }

            if ($isPscItem) {
                // Only keep PSC billing when the new plan allows it.
                if ($newPlan->allowsCompanyPsc()) {
                    $phaseTwoItems[] = $this->toScheduleItem($item);
                }
                continue;
            }

            if ($isMetered) {
                if (in_array($item->price->id, $allowedMailPriceIds, true)) {
                    $phaseTwoItems[] = $this->toScheduleItem($item);
                }
                continue;
            }

            // Unknown add-on: keep it rather than silently dropping billing.
            $phaseTwoItems[] = $this->toScheduleItem($item);
        }

        $schedule = $stripe->subscriptionSchedules->create([
            'from_subscription' => $subscription->stripe_id,
        ]);

        $endDate = $schedule->phases[0]->end_date ?? $stripeSubscription->current_period_end;

        $stripe->subscriptionSchedules->update($schedule->id, [
            'end_behavior' => 'release',
            'phases' => [
                [
                    'items' => $phaseOneItems,
                    'start_date' => $schedule->phases[0]->start_date,
                    'end_date' => $endDate,
                    'proration_behavior' => 'none',
                ],
                [
                    'items' => $phaseTwoItems,
                    'proration_behavior' => 'none',
                ],
            ],
        ]);

        $subscription->forceFill([
            'subscription_schedule_id' => $schedule->id,
            'scheduled_plan_id' => $newPlan->id,
            'scheduled_change_at' => Carbon::createFromTimestamp($endDate),
            'scheduled_change_type' => 'downgrade',
        ])->save();

        return $subscription;
    }

    /**
     * Release a pending Stripe subscription schedule and clear the local
     * pending-change state.
     *
     * The schedule id is normally tracked locally, but we also fall back to
     * asking Stripe, so a schedule that exists on Stripe but is missing from
     * the local row (e.g. an earlier attempt that failed before saving) is
     * still cleaned up. Without this, `from_subscription` would be rejected
     * with "You cannot migrate a subscription that is already attached to a
     * schedule".
     */
    public function cancelScheduledChange(PlanSubscription $subscription): void
    {
        $scheduleId = $subscription->subscription_schedule_id ?: $this->findStripeScheduleId($subscription);

        if ($scheduleId) {
            try {
                $subscription->owner->stripe()
                    ->subscriptionSchedules->release($scheduleId);
            } catch (\Exception $e) {
                report($e);
            }
        }

        $this->clearScheduledChange($subscription);
    }

    /**
     * Ask Stripe whether the subscription is currently attached to a schedule.
     */
    protected function findStripeScheduleId(PlanSubscription $subscription): ?string
    {
        try {
            $stripeSubscription = $subscription->owner->stripe()
                ->subscriptions->retrieve($subscription->stripe_id);
        } catch (\Exception $e) {
            report($e);

            return null;
        }

        return $this->scheduleIdFromSubscription($stripeSubscription);
    }

    /**
     * Normalise the `schedule` field of a Stripe subscription to an id (it may
     * be a plain id string, an expanded object, or absent).
     */
    protected function scheduleIdFromSubscription($stripeSubscription): ?string
    {
        $schedule = $stripeSubscription->schedule ?? null;

        if (is_string($schedule) && $schedule !== '') {
            return $schedule;
        }

        if (is_object($schedule) && ! empty($schedule->id)) {
            return $schedule->id;
        }

        return null;
    }

    /**
     * Cancel the subscription at period end (grace period) and notify the user.
     */
    public function cancel(User $user): PlanSubscription
    {
        $subscription = $this->requireActiveSubscription($user);

        // Remove any pending downgrade so cancellation is clean. This also
        // clears a schedule that exists on Stripe but is missing locally, so
        // Stripe will accept the cancellation.
        $this->cancelScheduledChange($subscription);
        $subscription->refresh();

        $subscription->cancel();
        $subscription->refresh();

        SendSubscriptionCancelledEmail::dispatch($user, $subscription->ends_at, 'requested');

        return $subscription;
    }

    /**
     * Resume a subscription that is within its grace period.
     */
    public function resume(User $user): bool
    {
        $subscription = $user->subscription('default');

        if (! $subscription || ! $subscription->onGracePeriod()) {
            return false;
        }

        $subscription->resume();

        return true;
    }

    protected function requireActiveSubscription(User $user): PlanSubscription
    {
        $subscription = $user->subscription('default');

        if (! $subscription || ! $subscription->active() || $subscription->onGracePeriod()) {
            throw new NoActiveStripeSubscriptionException();
        }

        return $subscription;
    }

    protected function clearScheduledChange(PlanSubscription $subscription): void
    {
        $subscription->forceFill([
            'subscription_schedule_id' => null,
            'scheduled_plan_id' => null,
            'scheduled_change_at' => null,
            'scheduled_change_type' => null,
        ])->save();
    }

    protected function priceIdForInterval(Plan $plan, string $interval): ?string
    {
        return $interval === SubscriptionTypeEnum::YEARLY->value
            ? $plan->stripe_price_id_yearly
            : $plan->stripe_price_id_monthly;
    }

    /**
     * Find the subscription item that represents the virtual-address plan
     * (as opposed to PSC or metered mail add-ons).
     */
    protected function findPlanItem(PlanSubscription $subscription, ?Plan $currentPlan)
    {
        $priceIds = $currentPlan
            ? array_filter([$currentPlan->stripe_price_id_monthly, $currentPlan->stripe_price_id_yearly])
            : [];

        if (empty($priceIds)) {
            $priceIds = Plan::query()
                ->get(['stripe_price_id_monthly', 'stripe_price_id_yearly'])
                ->flatMap(fn ($plan) => array_filter([
                    $plan->stripe_price_id_monthly,
                    $plan->stripe_price_id_yearly,
                ]))
                ->all();
        }

        return $subscription->items->first(fn ($item) => in_array($item->stripe_price, $priceIds, true));
    }

    /**
     * Convert a Stripe subscription item into the shape expected by a
     * subscription schedule phase.
     */
    protected function toScheduleItem($item): array
    {
        $params = ['price' => $item->price->id];

        $usageType = $item->price->recurring->usage_type ?? 'licensed';
        if ($usageType !== 'metered' && isset($item->quantity)) {
            $params['quantity'] = $item->quantity;
        }

        return $params;
    }
}
