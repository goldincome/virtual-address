<?php

namespace App\Observers;

use Exception;
use App\Models\PscType;
use Stripe\StripeClient;
use Illuminate\Contracts\Queue\ShouldQueue;

class PscTypeObserver implements ShouldQueue
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
    }

    public function created(PscType $pscType): void
    {
        try {
            $stripeProduct = $this->stripe->products->create([
                'name' => $pscType->name,
                'description' => $pscType->description ?? $pscType->name,
                'type' => 'service',
            ]);

            $stripePriceMonthly = $this->stripe->prices->create([
                'product' => $stripeProduct->id,
                'unit_amount' => $pscType->price_monthly * 100,
                'currency' => config('cashier.currency'),
                'recurring' => ['interval' => 'month'],
            ]);

            $stripePriceYearly = $this->stripe->prices->create([
                'product' => $stripeProduct->id,
                'unit_amount' => $pscType->price_yearly * 100,
                'currency' => config('cashier.currency'),
                'recurring' => ['interval' => 'year'],
            ]);

            $pscType->withoutEvents(function () use ($pscType, $stripeProduct, $stripePriceMonthly, $stripePriceYearly) {
                $pscType->update([
                    'stripe_product_id' => $stripeProduct->id,
                    'stripe_price_id_monthly' => $stripePriceMonthly->id,
                    'stripe_price_id_yearly' => $stripePriceYearly->id,
                ]);
            });
        } catch (Exception $e) {
            report($e);
            $pscType->delete();
            throw new Exception('Failed to create Stripe entities: ' . $e->getMessage());
        }
    }

    public function updated(PscType $pscType): void
    {
        try {
            if ($pscType->isDirty(['name', 'description']) && $pscType->stripe_product_id) {
                $this->stripe->products->update($pscType->stripe_product_id, [
                    'name' => $pscType->name,
                    'description' => $pscType->description ?? $pscType->name,
                ]);
            }

            $newPrices = [];
            if ($pscType->isDirty('price_monthly') && $pscType->stripe_product_id) {
                if ($pscType->getOriginal('stripe_price_id_monthly')) {
                    $this->stripe->prices->update($pscType->getOriginal('stripe_price_id_monthly'), ['active' => false]);
                }
                $newMonthly = $this->stripe->prices->create([
                    'product' => $pscType->stripe_product_id,
                    'unit_amount' => $pscType->price_monthly * 100,
                    'currency' => config('cashier.currency'),
                    'recurring' => ['interval' => 'month'],
                ]);
                $newPrices['stripe_price_id_monthly'] = $newMonthly->id;
            }

            if ($pscType->isDirty('price_yearly') && $pscType->stripe_product_id) {
                if ($pscType->getOriginal('stripe_price_id_yearly')) {
                    $this->stripe->prices->update($pscType->getOriginal('stripe_price_id_yearly'), ['active' => false]);
                }
                $newYearly = $this->stripe->prices->create([
                    'product' => $pscType->stripe_product_id,
                    'unit_amount' => $pscType->price_yearly * 100,
                    'currency' => config('cashier.currency'),
                    'recurring' => ['interval' => 'year'],
                ]);
                $newPrices['stripe_price_id_yearly'] = $newYearly->id;
            }

            if (!empty($newPrices)) {
                $pscType->withoutEvents(fn() => $pscType->update($newPrices));
            }
        } catch (Exception $e) {
            report($e);
            throw new Exception('Failed to update Stripe entities: ' . $e->getMessage());
        }
    }

    public function deleted(PscType $pscType): void
    {
        try {
            if ($pscType->stripe_product_id) {
                $this->stripe->products->update($pscType->stripe_product_id, ['active' => false]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
