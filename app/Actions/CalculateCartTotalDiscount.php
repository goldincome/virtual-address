<?php
namespace App\Actions;

use App\Enums\ProductTypeEnum;
use Gloudemans\Shoppingcart\Facades\Cart;

class CalculateCartTotalDiscount
{
     // Calculate original subtotal and discount for this item
    public function execute()
    {
        $totalDiscount = 0;
        if(count(Cart::content()) > 0){
            foreach(Cart::content() as $item){
                // Only room/booking items carry booking_time_raw; others use the unit quantity
                $itemQty = ($item->options->type !== ProductTypeEnum::VIRTUAL_ADDRESS->value && !empty($item->options->booking_time_raw))
                    ? count($item->options->booking_time_raw)
                    : $item->qty;
                $itemDiscount = ($item->options->discount_amount ?? 0) * $itemQty;

                // Add to grand totals
                $totalDiscount += $itemDiscount;
            }
        }
        return $totalDiscount;
    }

}