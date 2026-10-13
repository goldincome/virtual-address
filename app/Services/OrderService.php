<?php
namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\PaymentReceipt;
use App\Enums\ProductTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Jobs\SendPaymentReceiptEmail;
use Illuminate\Support\Facades\DB;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Actions\CalculateCartTotalDiscount;
use Illuminate\Database\UniqueConstraintViolationException;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

class OrderService
{

   public function createOrder(array $orderData): Order
    {   
        foreach(Cart::content() as $cartItem) { 
            $orderData['payment_method'] = $cartItem->options->payment_method;
        }
        
        $orderNo = app(CartService::class)->getOrderNoFromCartItem();
        try{
            DB::beginTransaction();

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
            //create order details
            $this->createOrderDetails($order);
             DB::commit();

            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Order creation failed');
        }
    }


   /**
    * Mark an order as paid and send the payment confirmation (receipt with
    * invoice + service agreement) exactly once per Stripe invoice.
    *
    * Stripe fires `invoice.payment_succeeded` as soon as the card is charged,
    * which can be *before* the browser is redirected back and the order row is
    * created. Both the webhook and the success redirect therefore call this
    * method, passing the same Stripe invoice id; the unique `payment_receipts`
    * row makes the confirmation idempotent regardless of which one arrives first.
    *
    * @return bool true when this call confirmed the payment, false when it was
    *              already confirmed by the other path.
    */
    public function confirmPayment(Order $order, ?string $stripeInvoiceId = null): bool
    {
        $key = $stripeInvoiceId ?: 'order-' . $order->id;

        try {
            PaymentReceipt::create([
                'stripe_invoice_id' => $key,
                'order_id' => $order->id,
                'sent_at' => now(),
            ]);
        } catch (UniqueConstraintViolationException $e) {
            // Already confirmed by the webhook/redirect for this invoice.
            return false;
        }

        $order->update(['status' => PaymentStatusEnum::Paid->value]);

        SendPaymentReceiptEmail::dispatch($order, $key);

        return true;
    }

    /**
     * Create an order (and its snapshot detail) for an in-place plan change
     * (upgrade). Unlike createOrder() this does not read the shopping cart:
     * a plan change is processed directly against the existing subscription.
     */
    public function createPlanChangeOrder(User $user, Plan $plan, string $interval): Order
    {
        $isYearly = $interval === SubscriptionTypeEnum::YEARLY->value;
        $price = (float) ($isYearly ? $plan->yearly_monthly_price : $plan->price);
        $orderNo = generateOrderNumber();

        $planSnapshot = $plan->toArray();
        $planSnapshot['subscription_type'] = $interval;

        $features = $plan->features()->with('featureSetting')->get()->map(function ($feature) {
            return [
                'id' => optional($feature->featureSetting)->id,
                'name' => optional($feature->featureSetting)->name,
            ];
        })->all();

        $order = Order::create([
            'user_id' => $user->id,
            'order_no' => $orderNo,
            'payment_method' => 'stripe',
            'total' => $price,
            'sub_total' => $price,
            'tax' => 0,
            'discount' => 0,
            'currency' => config('cashier.currency'),
            'status' => PaymentStatusEnum::Pending->value,
        ]);

        $order->orderDetails()->create([
            'user_id' => $user->id,
            'name' => trim(($plan->product->name ?? 'Virtual Address') . ' - ' . $plan->name),
            'ref_no' => generateOrderNumber(),
            'order_id' => $order->id,
            'product_id' => $plan->product_id,
            'product_type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
            'quantity' => 1,
            'price' => $price,
            'sub_total' => $price,
            'plan_id' => $plan->id,
            'features' => $features ? json_encode($features) : null,
            'plan' => json_encode($planSnapshot),
            'discounts' => null,
        ]);

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