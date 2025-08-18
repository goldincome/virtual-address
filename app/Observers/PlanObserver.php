<?php

namespace App\Observers;

use Exception;
use Stripe\Stripe;
use App\Models\Plan;
use Stripe\StripeClient;
use Illuminate\Contracts\Queue\ShouldQueue;

class PlanObserver implements ShouldQueue
{
     protected $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
    }

    public function created(Plan $plan): void
    {
        try {
            // Step 1 & 2: Create Stripe Product and Prices
            $stripeProduct = $this->stripe->products->create(['name' => $plan->name, 'description' => $plan->description]);
            $stripePriceMonthly = $this->stripe->prices->create([
                'product' => $stripeProduct->id,
                'unit_amount' => $plan->price * 100,
                'currency' => $plan->currency,
                'recurring' => ['interval' => 'month'],
            ]);
            $stripePriceYearly = $this->stripe->prices->create([
                'product' => $stripeProduct->id,
                'unit_amount' => $plan->yearly_monthly_price * 100,
                'currency' => $plan->currency,
                'recurring' => ['interval' => 'year'],
            ]);

            $stripeCouponId = null;
            $stripePromotionCodeId = null;

            // Step 3: Create discount if applicable
            if ($plan->discount_percent > 0 || $plan->discount_amount > 0) {
                [$stripeCouponId, $stripePromotionCodeId] = $this->createStripeDiscount($plan);
            }

            // Step 4: Update the local plan with all new Stripe IDs
            $plan->withoutEvents(function () use ($plan, $stripeProduct, $stripePriceMonthly, $stripePriceYearly, $stripeCouponId, $stripePromotionCodeId) {
                $plan->update([
                    'stripe_product_id' => $stripeProduct->id,
                    'stripe_price_id_monthly' => $stripePriceMonthly->id,
                    'stripe_price_id_yearly' => $stripePriceYearly->id,
                    'stripe_coupon_id' => $stripeCouponId,
                    'stripe_promotion_code_id' => $stripePromotionCodeId,
                ]);
            });

        } catch (Exception $e) {
            report($e);
            $plan->delete();
            throw new Exception("Failed to create Stripe entities: " . $e->getMessage());
        }
    }

    /**
     * Handle the Plan "updated" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function updated(Plan $plan): void
    {

        try {
            // Update Product Name/Description
            if ($plan->isDirty(['name', 'description'])) {
                $this->stripe->products->update($plan->stripe_product_id, [
                    'name' => $plan->name,
                    'description' => $plan->description,
                ]);
            }

            // Update Prices (Archive old, create new)
            $newPrices = [];
            if ($plan->isDirty('price')) {
                $this->stripe->prices->update($plan->stripe_price_id_monthly, ['active' => false]);
                $newMonthlyPrice = $this->stripe->prices->create([
                    'product' => $plan->stripe_product_id,
                    'unit_amount' => $plan->price * 100,
                    'currency' => $plan->currency,
                    'recurring' => ['interval' => 'month'],
                ]);
                $newPrices['stripe_price_id_monthly'] = $newMonthlyPrice->id;
            }
            if ($plan->isDirty('yearly_monthly_price')) {
                $this->stripe->prices->update($plan->stripe_price_id_yearly, ['active' => false]);
                $newYearlyPrice = $this->stripe->prices->create([
                    'product' => $plan->stripe_product_id,
                    'unit_amount' => $plan->yearly_monthly_price * 100,
                    'currency' => $plan->currency,
                    'recurring' => ['interval' => 'year'],
                ]);
                $newPrices['stripe_price_id_yearly'] = $newYearlyPrice->id;
            }
            if (!empty($newPrices)) {
                $plan->withoutEvents(fn() => $plan->update($newPrices));
            }

            // Update Discounts (Deactivate old, create new)
            if ($plan->isDirty(['discount_percent', 'discount_amount', 'discount_duration_in_months'])) {
                // Deactivate the old promotion code and coupon
                if ($plan->getOriginal('stripe_promotion_code_id')) {
                    $this->stripe->promotionCodes->update($plan->getOriginal('stripe_promotion_code_id'), ['active' => false]);
                }
                if ($plan->getOriginal('stripe_coupon_id')) {
                    $this->stripe->coupons->update($plan->getOriginal('stripe_coupon_id'), ['active' => false]);
                }

                // Create new ones if a discount is still specified
                [$stripeCouponId, $stripePromotionCodeId] = [null, null];
                if ($plan->discount_percent > 0 || $plan->discount_amount > 0) {
                     [$stripeCouponId, $stripePromotionCodeId] = $this->createStripeDiscount($plan);
                }
                
                $plan->withoutEvents(fn() => $plan->update([
                    'stripe_coupon_id' => $stripeCouponId,
                    'stripe_promotion_code_id' => $stripePromotionCodeId,
                ]));
            }

        } catch (Exception $e) {
            report($e);
            // Optionally, revert changes or log a critical failure
            throw new Exception("Failed to update Stripe entities: " . $e->getMessage());
        }
    }

    /**
     * Helper function to create a Stripe Coupon and Promotion Code.
     *
     * @param Plan $plan
     * @return array
     */
    private function createStripeDiscount(Plan $plan): array
    {
        $couponDetails = [];
        if ($plan->discount_percent > 0) {
            $couponDetails = [
                'percent_off' => $plan->discount_percent,
                'name' => $plan->name . ' ' . $plan->discount_percent . '% Discount',
            ];
        } elseif ($plan->discount_amount > 0) {
            $couponDetails = [
                'amount_off' => $plan->discount_amount * 100,
                'currency' => $plan->currency,
                'name' => $plan->name . ' £' . $plan->discount_amount . ' Discount',
            ];
        }

        if (empty($couponDetails)) {
            return [null, null];
        }

        $couponDetails['duration'] = 'repeating';
        $couponDetails['duration_in_months'] = $plan->discount_duration_in_months;

        $stripeCoupon = $this->stripe->coupons->create($couponDetails);

        $promoCodeString = strtoupper($plan->slug . '_DISCOUNT');
        $stripePromotionCode = $this->stripe->promotionCodes->create([
            'coupon' => $stripeCoupon->id,
            'code' => $promoCodeString,
        ]);

        return [$stripeCoupon->id, $stripePromotionCode->id];
    }
}
