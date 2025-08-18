<?php
namespace App\Services;

use App\Enums\DiscountTypeEnum;
use App\Models\Plan;
use App\Models\Product;
use App\Models\PlanRoomDiscount;
use Illuminate\Pagination\LengthAwarePaginator;


class PlanRoomDiscountService
{
    /**
     * Get a paginated list of plan discounts.
     *
     * @return LengthAwarePaginator
     */
    public function getDiscounts(): LengthAwarePaginator
    {
        return PlanRoomDiscount::with(['plan', 'product'])->latest()->paginate(15);
    }
    /**
     * Get discounts by plan and product ID.
     *
     * @param Plan $plan
     * @param Product $product
     * @param float $prodPrice
     * @return array
     */
    public function getDiscountsByPlanAndProductId(Plan $plan, Product $product): array
    { 
        $prodPrice = $product->price;
        if ($prodPrice <= 0 || !$plan || !$product) {
            return [];
        }
        // Fetch the discount for the given plan and product
       $planRoomDiscount = PlanRoomDiscount::where('plan_id', $plan->id)
            ->where('product_id', $product->id)
            ->first();
           
        if ($planRoomDiscount) {
            if ($planRoomDiscount->discount_type === DiscountTypeEnum::FLAT->value) {
                $discountAmount = $planRoomDiscount->discount_value > $product->price ?  0 : $planRoomDiscount->discount_value;
                //$discountAmount = $planRoomDiscount->discount_value;
            } else {
                //discount_type is 'percent'
                $planRoomDiscount->discount_value = $planRoomDiscount->discount_value > 100 ?  100 : $planRoomDiscount->discount_value;
                $discountAmount =  ($prodPrice * ($planRoomDiscount->discount_value/ 100));
            }
            $discountedPrice = $prodPrice - $discountAmount;
            $discountValue = $planRoomDiscount->discount_type->value === DiscountTypeEnum::PERCENTAGE->value ? $planRoomDiscount->discount_value . '%' : currencyFormatter($planRoomDiscount->discount_value);
            return [
                'discounted_price' => number_format($discountedPrice, 2),
                'discount_amount' => number_format($discountAmount, 2),
                'discount_value' => $discountValue,
                'product_price' => number_format($prodPrice, 2),
                'discount_type' => $planRoomDiscount->discount_type->value,
            ];
        }
        else {
            return [];
        }
    }

    /**
     * Create a new plan discount.
     *
     * @param array $data
     * @return PlanDiscount
     */
    public function createDiscount(array $data): PlanRoomDiscount
    {
        return PlanRoomDiscount::create($data);
    }

    /**
     * Update an existing plan discount.
     *
     * @param PlanDiscount $planDiscount
     * @param array $data
     * @return PlanDiscount
     */
    public function updateDiscount(PlanRoomDiscount $planRoomDiscount, array $data): PlanRoomDiscount
    {
        $planRoomDiscount->update($data);
        return $planRoomDiscount;
    }

    /**
     * Delete a plan discount.
     *
     * @param PlanDiscount $planDiscount
     * @return void
     */
    public function deleteDiscount(PlanRoomDiscount $planRoomDiscount): void
    {
        $planRoomDiscount->delete();
    }
}
    