<?php

namespace App\Models;

use Laravel\Cashier\Subscription;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\SubscriptionItem;

class PlanSubscription extends Subscription
{
    protected $table = 'subscriptions';

     protected $casts = [
        'ends_at' => 'datetime',
        'quantity' => 'integer',
        'trial_ends_at' => 'datetime',
        'ending_at' => 'datetime',
    ];

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
     * This is the core of the solution. Since a subscription in Cashier
     * is linked to a specific Stripe Price ID (`stripe_price`), we need
     * to find which of our application's plans contains that price ID.
     * A plan can have a monthly or a yearly price, so we check both columns.
     *
     * We use a modern accessor to define this relationship.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function plan(): Attribute
    {
        return Attribute::get(function () {
            // The 'stripe_price' column on the 'subscriptions' table holds the
            // specific price ID (monthly or yearly) the user is subscribed to.
           $stripePriceId = $this->items->first()->stripe_price;

            // Find the Plan in our database where either the monthly or yearly
            // Stripe Price ID matches the one on this subscription.
            return Plan::where('stripe_price_id_monthly', $stripePriceId)
                       ->orWhere('stripe_price_id_yearly', $stripePriceId)
                       ->first();
        });
    }
}

