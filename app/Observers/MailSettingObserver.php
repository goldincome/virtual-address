<?php

namespace App\Observers;

use Stripe\StripeClient;
use App\Models\MailSetting;
use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailSettingObserver implements ShouldQueue
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
    }
    /**
     * Handle the MailSetting "created" event.
     *
     * @param  \App\Models\MailSetting  $mailSetting
     * @return void
     */
    public function created(MailSetting $mailSetting): void
    {
        try{
            $stripeProduct = $this->stripe->products->create([
                'name' => $mailSetting->name ,
                'type' => 'service',
            ]);

            // 2. Create a metered Price for that new Product
            $stripePrice = $this->stripe->prices->create([
                'product' => $stripeProduct->id,
                'unit_amount' => $mailSetting->price * 100, // You can set this to 1 cent or a base amount if needed
                'currency' => config('cashier.currency'),
                'billing_scheme' => 'per_unit',
                'recurring' => [
                    'interval' => $mailSetting->interval,
                    'usage_type' => 'metered',
                ],
                'lookup_key' => $mailSetting->stripe_price_name,
            ]);

            // 3. Store the Stripe IDs in the MailSetting model
            $mailSetting->withoutEvents(function () use ($mailSetting, $stripeProduct, $stripePrice) {
                $mailSetting->update([
                    'stripe_product_id' => $stripeProduct->id,
                    'stripe_price_id' => $stripePrice->id,
                ]);
            });
        } catch (\Exception $e) {
            report($e);
            $mailSetting->delete();
            throw new \Exception("Failed to create Stripe entities: " . $e->getMessage());
        }
    }

    /**
     * Handle the MailSetting "updated" event.
     *
     * @param  \App\Models\MailSetting  $mailSetting
     * @return void
     */
    public function updated(MailSetting $mailSetting): void
    {
        try{
            // Update Product Name
            if ($mailSetting->isDirty('name') && $mailSetting->stripe_product_id) {
                $this->stripe->products->update(
                    $mailSetting->stripe_product_id,
                    ['name' => $mailSetting->name]
                );
            }

            // Update Prices (Archive old, create new) - follows PlanObserver pattern
            if (($mailSetting->isDirty('price') || $mailSetting->isDirty('interval')) && $mailSetting->stripe_price_id) {
                $oldPriceId = $mailSetting->getOriginal('stripe_price_id');
                
                // Deactivate the old Stripe price
                if ($oldPriceId) {
                    try {
                        $this->stripe->prices->update($oldPriceId, ['active' => false]);
                    } catch (\Exception $e) {
                        // Old price may not exist; continue anyway
                        report($e);
                    }
                }

                // Create new metered price with updated amount and/or interval
                $params = [
                    'product' => $mailSetting->stripe_product_id,
                    'unit_amount' => (int) round($mailSetting->price * 100),
                    'currency' => config('cashier.currency'),
                    'billing_scheme' => 'per_unit',
                    'recurring' => [
                        'interval' => $mailSetting->interval,
                        'usage_type' => 'metered',
                    ],
                ];

                if ($mailSetting->stripe_price_name) {
                    $params['lookup_key'] = $mailSetting->stripe_price_name;
                }

                try {
                    $newStripePrice = $this->stripe->prices->create($params);
                } catch (\Exception $e) {
                    // Retry without lookup_key if it causes a conflict
                    unset($params['lookup_key']);
                    $newStripePrice = $this->stripe->prices->create($params);
                }

                // Save new Stripe price ID without triggering events
                $mailSetting->withoutEvents(function () use ($mailSetting, $newStripePrice) {
                    $mailSetting->update([
                        'stripe_price_id' => $newStripePrice->id,
                    ]);
                });

                // Repoint existing subscriber metered subscription items to the new price
                $this->repointSubscriptionItems($oldPriceId, $newStripePrice->id);
            }
           
        } catch (\Exception $e) {
            report($e);
            throw new \Exception("Failed to update Stripe entities: " . $e->getMessage());
        }
    }

    /**
     * Repoint existing subscriber metered subscription items from old price to new price.
     *
     * @param  string|null  $oldPriceId
     * @param  string  $newPriceId
     * @return void
     */
    protected function repointSubscriptionItems(?string $oldPriceId, string $newPriceId): void
    {
        if (!$oldPriceId) {
            return;
        }

        try {
            // Find all active subscriptions with items using the old price
            $subscriptions = $this->stripe->subscriptions->all([
                'status' => 'active',
                'limit' => 100,
                'expand' => ['data.items'],
            ]);

            foreach ($subscriptions->data as $subscription) {
                foreach ($subscription->items->data ?? [] as $item) {
                    if (($item->price->id ?? null) === $oldPriceId) {
                        // Update the subscription item to point to the new price
                        $this->stripe->subscriptionItems->update($item->id, [
                            'price' => $newPriceId,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            // Log but don't fail the entire update if repointing fails
            report($e);
        }
    }

    /**
     * Handle the MailSetting "deleted" event.
     *
     * @param  \App\Models\MailSetting  $mailSetting
     * @return void
     */
    public function deleted(MailSetting $mailSetting): void
    {
        // You could dispatch a job here to archive the product in Stripe if needed.
        try {
            if ($mailSetting->stripe_product_id) {
                $this->stripe->products->update($mailSetting->stripe_product_id, ['active' => false]);
            }
        } catch (\Exception $e) {
            report($e);
            throw new \Exception("Failed to deactivate Stripe product: " . $e->getMessage());
        }
    }
}
