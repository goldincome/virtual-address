<?php

namespace App\Models;

use Laravel\Cashier\Subscription;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\SubscriptionItem;

class PlanSubscription extends Subscription
{
    protected $table = 'subscriptions';

    protected $casts = [
        'ends_at' => 'datetime',
        'quantity' => 'integer',
        'trial_ends_at' => 'datetime',
        'ending_at' => 'datetime',
        'scheduled_change_at' => 'datetime',
    ];

    /**
     * Cache for the resolved plan so the accessor only runs once per model.
     */
    protected $cachedPlan = false;

    /**
     * Get the items related to the subscription.
     *
     * This method OVERRIDES the default relationship in Cashier's Subscription model.
     * The error occurs because by default, Eloquent assumes the foreign key on the
     * subscription_items table is 'plan_subscription_id' based on this model's name.
     * We must explicitly tell it that the foreign key is 'subscription_id'.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class, 'subscription_id');
    }

    /**
     * Get the plan that owns the subscription.
     *
     * This is the core of the solution. A Cashier subscription links to one or
     * more Stripe Price IDs (one `stripe_price` per subscription item), so we
     * search every item and pick the plan that contains one of those price IDs.
     * A plan can have a monthly or a yearly price, so we check both columns.
     * Returns null when no item maps to a known plan instead of throwing.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function plan(): Attribute
    {
        return Attribute::get(function () {
            if ($this->cachedPlan !== false) {
                return $this->cachedPlan;
            }

            $this->cachedPlan = $this->resolvePlan();

            return $this->cachedPlan;
        });
    }

    /**
     * The plan this subscription is scheduled to change to at period end.
     */
    public function scheduledPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'scheduled_plan_id');
    }

    /**
     * Whether a downgrade/change is scheduled to take effect at period end.
     */
    public function hasScheduledChange(): bool
    {
        return ! empty($this->subscription_schedule_id)
            && ! empty($this->scheduled_change_at)
            && $this->scheduled_change_at->isFuture();
    }

    /**
     * Find the application plan linked to any of this subscription's price IDs.
     */
    public function resolvePlan(): ?Plan
    {
        return $this->items
            ->pluck('stripe_price')
            ->filter()
            ->unique()
            ->map(function (string $stripePriceId) {
                return Plan::where('stripe_price_id_monthly', $stripePriceId)
                           ->orWhere('stripe_price_id_yearly', $stripePriceId)
                           ->first();
            })
            ->first(fn ($plan) => ! is_null($plan));
    }
}

