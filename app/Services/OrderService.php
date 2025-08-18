<?php
namespace App\Services;

use App\Actions\CalculateCartTotalDiscount;
use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Enums\PaymentStatusEnum;
use Gloudemans\Shoppingcart\Facades\Cart;


class OrderService
{

   public function createOrder(array $orderData): Order
    {   
        foreach(Cart::content() as $cartItem) { 
            $orderData['payment_method'] = $cartItem->options->payment_method;
        }
        
        $orderNo = app(CartService::class)->getOrderNoFromCartItem();
        try{
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_no' => $orderNo,
                'payment_method' => $orderData['payment_method'],
                'total' => Cart::total(),
                'sub_total' => Cart::subtotal(),
                'tax' => Cart::tax(),
                'discount' => app(CalculateCartTotalDiscount::class)->execute(),
                'currency' => config('cashier.currency'),
                'status' => PaymentStatusEnum::Pending->value,
            ]);
        } catch (\Exception $e) {
    
            throw new \Exception('Order creation failed');
        }
        
        //create order details
        $this->createOrderDetails($order);
        
        return $order;
    }


    protected function createOrderDetails(Order $order)
    {   
        foreach(Cart::content() as $index =>  $cartItem) {
            
            $orderDetail = OrderDetail::create([
                'user_id' => auth()->id(),
                'name' => $cartItem->name,
                'ref_no' => generateOrderNumber(),
                'order_id' => $order->id,
                'product_id' => $cartItem->options->product_model_id,
                'product_type' => $cartItem->options->type,
                'quantity' => $cartItem->qty,
                'price' => $cartItem->price,
                'sub_total' => $cartItem->subtotal,
                'booked_date' => $cartItem->options->booking_date ?? null,
                'all_booked_time' => $cartItem->options->booking_time_raw ? getAllBookingTimeJson($cartItem->options->booking_date, $cartItem->options->booking_time_raw) : null,
                'plan_id' => $cartItem->options->plan_id ?? null,
                'features' => $cartItem->options->features ? json_encode($cartItem->options->features) : null,
                'plan' => $cartItem->options->plan ? json_encode($cartItem->options->plan) : null,
                'discounts' => $cartItem->options->all_discount_data ? json_encode($cartItem->options->all_discount_data) : null,

            ]);
            if($cartItem->options->plan_id){
                //$user = User::find(auth()->id());
                //$plan = Plan::find($cartItem->options->plan_id);
                //subscribe user to a plan
                //$userSubsciption = $user->newPlanSubscription($plan->name, $plan);
                //$subscription = $userSubsciption->select('id','name','starts_at', 'ends_at')->first()->toArray();
                //$orderDetail->update(['subscription' => json_encode($subscription)]);
            }
        }
    }

}