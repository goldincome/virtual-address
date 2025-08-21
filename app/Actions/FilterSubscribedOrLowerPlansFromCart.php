<?php
namespace App\Actions;

use App\Models\Plan;
use App\Services\CartService;
use App\Enums\ProductTypeEnum;
use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\Facades\Cart;

class FilterSubscribedOrLowerPlansFromCart
{


    public function execute(){

        foreach (Cart::content() as $item) {
            if ($item->options->type === ProductTypeEnum::VIRTUAL_ADDRESS->value) {
                $cartPlan = app(CartService::class)->getPlanFromCart();
                if ($cartPlan) {
                    $cartPlan = Plan::find($cartPlan['id']);
                    if (auth()->user() && auth()->user()->subscribed('default')) // user has a plan 
                    {
                        $subscribedPlan =  auth()->user()->subscription('default')->plan ?? null;
                        if($cartPlan->id == $subscribedPlan->id || $subscribedPlan->level > $cartPlan->level){
                            app(CartService::class)->remove($item->rowId);
                        }
                    }
                }
            }
           
        }
        
    }

}