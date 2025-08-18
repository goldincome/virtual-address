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
            //Todo: First implement if the interval is changed, create a new metered name and price 
            //
            if ($mailSetting->isDirty('name') && $mailSetting->stripe_product_id) {
                $this->stripe->products->update(
                    $mailSetting->stripe_product_id,
                    ['name' => $mailSetting->name]
                );
            }

            if ($mailSetting->isDirty('price') && $mailSetting->stripe_price_id) {
                $this->stripe->prices->update(
                    $mailSetting->stripe_price_id,
                    ['unit_amount' => $mailSetting->price * 100]
                );
            }
           
        } catch (\Exception $e) {
            report($e);
            throw new \Exception("Failed to update Stripe entities: " . $e->getMessage());
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
