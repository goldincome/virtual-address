<?php

namespace App\Observers;

use App\Models\CompanyType;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Stripe\StripeClient;

class CompanyTypeObserver implements ShouldQueue
{
    protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
    }

    public function created(CompanyType $companyType): void
    {
        try {
            $stripeProduct = $this->stripe->products->create([
                'name' => $companyType->name,
                'description' => $companyType->description ?? $companyType->name,
                'type' => 'service',
            ]);

            $stripePriceMonthly = $this->stripe->prices->create([
                'product' => $stripeProduct->id,
                'unit_amount' => (int) round($companyType->price_monthly * 100),
                'currency' => config('cashier.currency'),
                'recurring' => ['interval' => 'month'],
            ]);

            $stripePriceYearly = $this->stripe->prices->create([
                'product' => $stripeProduct->id,
                'unit_amount' => (int) round($companyType->price_yearly * 100),
                'currency' => config('cashier.currency'),
                'recurring' => ['interval' => 'year'],
            ]);

            $companyType->withoutEvents(function () use ($companyType, $stripeProduct, $stripePriceMonthly, $stripePriceYearly) {
                $companyType->update([
                    'stripe_product_id' => $stripeProduct->id,
                    'stripe_price_id_monthly' => $stripePriceMonthly->id,
                    'stripe_price_id_yearly' => $stripePriceYearly->id,
                ]);
            });
        } catch (Exception $e) {
            report($e);
            $companyType->delete();
            throw new Exception('Failed to create Stripe entities: ' . $e->getMessage());
        }
    }

    public function updated(CompanyType $companyType): void
    {
        try {
            if ($companyType->isDirty(['name', 'description']) && $companyType->stripe_product_id) {
                $this->stripe->products->update($companyType->stripe_product_id, [
                    'name' => $companyType->name,
                    'description' => $companyType->description ?? $companyType->name,
                ]);
            }

            $newPrices = [];
            if ($companyType->isDirty('price_monthly') && $companyType->stripe_product_id) {
                if ($companyType->getOriginal('stripe_price_id_monthly')) {
                    $this->stripe->prices->update($companyType->getOriginal('stripe_price_id_monthly'), ['active' => false]);
                }
                $newMonthly = $this->stripe->prices->create([
                    'product' => $companyType->stripe_product_id,
                    'unit_amount' => (int) round($companyType->price_monthly * 100),
                    'currency' => config('cashier.currency'),
                    'recurring' => ['interval' => 'month'],
                ]);
                $newPrices['stripe_price_id_monthly'] = $newMonthly->id;
            }

            if ($companyType->isDirty('price_yearly') && $companyType->stripe_product_id) {
                if ($companyType->getOriginal('stripe_price_id_yearly')) {
                    $this->stripe->prices->update($companyType->getOriginal('stripe_price_id_yearly'), ['active' => false]);
                }
                $newYearly = $this->stripe->prices->create([
                    'product' => $companyType->stripe_product_id,
                    'unit_amount' => (int) round($companyType->price_yearly * 100),
                    'currency' => config('cashier.currency'),
                    'recurring' => ['interval' => 'year'],
                ]);
                $newPrices['stripe_price_id_yearly'] = $newYearly->id;
            }

            if (! empty($newPrices)) {
                $companyType->withoutEvents(fn () => $companyType->update($newPrices));
            }
        } catch (Exception $e) {
            report($e);
            throw new Exception('Failed to update Stripe entities: ' . $e->getMessage());
        }
    }

    public function deleted(CompanyType $companyType): void
    {
        try {
            if ($companyType->stripe_product_id) {
                $this->stripe->products->update($companyType->stripe_product_id, ['active' => false]);
            }
        } catch (Exception $e) {
            report($e);
        }
    }
}
