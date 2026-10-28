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
use Laravel\Cashier\Cashier;
use Laravel\Cashier\SubscriptionItem;

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
        if ($stripeInvoiceId) {
            try {
                $this->syncProrationFromInvoiceId($order, $stripeInvoiceId);
            } catch (\Exception $e) {
                report($e);
            }
        }

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

        try {
            if ($order->hasSubscription() && ! $order->user->subscription('default')) {
                $this->syncSubscriptionFromStripe($order);
            }
        } catch (\Exception $e) {
            report($e);
        }

        SendPaymentReceiptEmail::dispatch($order, $key);

        return true;
    }

    /**
     * Create (or return) a local order for a Stripe invoice that has no local
     * order yet — used for renewal cycles (including monthly mail
     * forwarding/scanning invoices) so the customer can see exactly what was
     * billed in the user panel and admin, and receives a receipt.
     *
     * Idempotent per Stripe invoice id.
     */
    public function createOrderFromInvoice(User $user, string $invoiceId): ?Order
    {
        $existing = Order::where('stripe_invoice_id', $invoiceId)->first();
        if ($existing) {
            return $existing;
        }

        try {
            $stripe = \Laravel\Cashier\Cashier::stripe();
            $invoice = $stripe->invoices->retrieve($invoiceId, [
                'expand' => ['lines.data', 'lines.data.price', 'lines.data.price.product'],
            ]);
        } catch (\Exception $e) {
            report($e);

            return null;
        }

        $currency = strtolower($invoice->currency ?? config('cashier.currency', 'gbp'));
        $isZeroDecimal = in_array($currency, ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf']);
        $divisor = $isZeroDecimal ? 1 : 100;

        $lines = $invoice->lines->data ?? [];
        if (empty($lines)) {
            return null;
        }

        $orderNo = generateOrderNumber();
        $subtotal = ((float) ($invoice->subtotal_excluding_tax ?? $invoice->subtotal ?? 0)) / $divisor;
        $tax = ((float) ($invoice->tax ?? 0)) / $divisor;
        $total = ((float) ($invoice->total ?? $invoice->amount_due ?? 0)) / $divisor;

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $user->id,
                'order_no' => $orderNo,
                'payment_method' => 'stripe',
                'payment_method_order_id' => $invoice->payment_intent ?? null,
                'stripe_invoice_id' => $invoiceId,
                'prorated_sub_total' => round($subtotal, 2),
                'prorated_tax' => round($tax, 2),
                'prorated_discount' => 0,
                'prorated_total' => round($total, 2),
                'proration_breakdown' => $this->normalizeInvoiceLines($invoice),
                'total' => round($subtotal + $tax, 2),
                'sub_total' => round($subtotal, 2),
                'tax' => round($tax, 2),
                'discount' => 0,
                'currency' => strtoupper($currency),
                'status' => PaymentStatusEnum::Pending->value,
            ]);

            foreach ($lines as $line) {
                $amount = ((float) ($line->amount ?? 0)) / $divisor;
                $quantity = (int) ($line->quantity ?? 1) ?: 1;
                $unitPrice = $amount / $quantity;
                [$productType, $planId] = $this->resolveLineProduct($line);

                $order->orderDetails()->create([
                    'user_id' => $user->id,
                    'name' => $line->description ?: 'Subscription charge',
                    'ref_no' => generateOrderNumber(),
                    'order_id' => $order->id,
                    'product_id' => null,
                    'product_type' => $productType,
                    'quantity' => $quantity,
                    'price' => round($unitPrice, 2),
                    'sub_total' => round($amount, 2),
                    'plan_id' => $planId,
                    'features' => null,
                    'plan' => null,
                    'discounts' => null,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return null;
        }

        return $order;
    }

    /**
     * Map a Stripe invoice line to a local product type (and plan when known).
     *
     * @return array{0: string, 1: int|null}
     */
    protected function resolveLineProduct($line): array
    {
        $priceId = $line->price->id ?? null;

        if ($priceId) {
            $plan = Plan::where('stripe_price_id_monthly', $priceId)
                ->orWhere('stripe_price_id_yearly', $priceId)
                ->first();
            if ($plan) {
                return [ProductTypeEnum::VIRTUAL_ADDRESS->value, $plan->id];
            }

            $mail = \App\Models\MailSetting::where('stripe_price_id', $priceId)->first();
            if ($mail) {
                return [ProductTypeEnum::MAIL_SERVICE->value, $mail->plan_id];
            }

            $psc = \App\Models\PscType::where('stripe_price_id_monthly', $priceId)
                ->orWhere('stripe_price_id_yearly', $priceId)
                ->first();
            if ($psc) {
                return [ProductTypeEnum::PSC->value, null];
            }

            $company = \App\Models\CompanyType::where('stripe_price_id_monthly', $priceId)
                ->orWhere('stripe_price_id_yearly', $priceId)
                ->first();
            if ($company) {
                return [ProductTypeEnum::COMPANY->value, null];
            }
        }

        return [ProductTypeEnum::MAIL_SERVICE->value, null];
    }

    public function syncProrationFromInvoiceId(Order $order, string $invoiceId): void
    {        if (empty($invoiceId) || str_starts_with($invoiceId, 'order-')) {
            return;
        }

        try {
            $stripe = \Laravel\Cashier\Cashier::stripe();
            $invoice = $stripe->invoices->retrieve($invoiceId, [
                'expand' => ['lines.data', 'lines.data.price', 'lines.data.price.product'],
            ]);
            $this->applyProrationToOrder($order, $invoice);
        } catch (\Exception $e) {
            report($e);
        }
    }

    protected function applyProrationToOrder(Order $order, $invoice): void
    {
        $currency = strtolower($invoice->currency ?? config('cashier.currency', 'gbp'));
        $isZeroDecimal = in_array($currency, ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf']);
        $divisor = $isZeroDecimal ? 1 : 100;

        $amountDueRaw = (float) ($invoice->amount_due ?? 0);
        $subtotalRaw = (float) ($invoice->subtotal_excluding_tax ?? $invoice->subtotal ?? $amountDueRaw);
        $taxRaw = (float) ($invoice->tax ?? 0);

        // Stripe amounts are in smallest currency unit; but be defensive in case they come as pounds
        if (! $isZeroDecimal && $amountDueRaw > 1000 && $amountDueRaw == round($amountDueRaw)) {
            $amountDue = $amountDueRaw / $divisor;
        } elseif (! $isZeroDecimal && $amountDueRaw > 0 && $amountDueRaw < 10000 && strpos((string)$amountDueRaw, '.') === false && $amountDueRaw % 100 == 0 && $amountDueRaw >= 100) {
            $amountDue = $amountDueRaw / $divisor;
        } else {
            $amountDue = $amountDueRaw / $divisor;
        }

        $subtotal = $subtotalRaw / $divisor;
        $tax = $taxRaw / $divisor;
        $totalDiscount = 0;

        if (isset($invoice->total_discount_amounts)) {
            foreach ($invoice->total_discount_amounts as $disc) {
                $discountRaw = (float) ($disc->amount ?? 0);
                $totalDiscount += abs($discountRaw / $divisor);
            }
        }

        $updateData = [
            'stripe_invoice_id' => $invoice->id ?? $order->stripe_invoice_id,
            'prorated_sub_total' => round($subtotal, 2),
            'prorated_tax' => round($tax, 2),
            'prorated_discount' => round($totalDiscount, 2),
            'prorated_total' => round($amountDue, 2),
            'proration_breakdown' => $this->normalizeInvoiceLines($invoice),
        ];

        if ($order->prorated_total === null || abs((float) $amountDue - (float) ($order->prorated_total ?? 0)) > 0.00001 || empty($order->proration_breakdown)) {
            $updateData['sub_total'] = round($subtotal, 2);
            $updateData['tax'] = round($tax, 2);
            $updateData['discount'] = round($totalDiscount, 2);
            $updateData['total'] = round($amountDue, 2);
        }

        $order->update($updateData);
        $this->applyProrationToPlanDetail($order, $invoice, $divisor);
    }

    protected function normalizeInvoiceLines($invoice): array
    {
        $currency = strtolower($invoice->currency ?? config('cashier.currency', 'gbp'));
        $isZeroDecimal = in_array($currency, ['bif', 'clp', 'djf', 'gnf', 'jpy', 'kmf', 'krw', 'mga', 'pyg', 'rwf', 'ugx', 'vnd', 'vuv', 'xaf', 'xof', 'xpf']);
        $divisor = $isZeroDecimal ? 1 : 100;
        $lines = [];
        if (isset($invoice->lines->data)) {
            foreach ($invoice->lines->data as $line) {
                $lines[] = [
                    'id' => $line->id ?? null,
                    'description' => $line->description ?? null,
                    'amount' => ((float) ($line->amount ?? 0)) / $divisor,
                    'quantity' => $line->quantity ?? null,
                    'proration' => $line->proration ?? false,
                    'period' => isset($line->period) ? [
                        'start' => $line->period->start ?? null,
                        'end' => $line->period->end ?? null,
                    ] : null,
                    'price_id' => $line->price->id ?? null,
                ];
            }
        }

        return $lines;
    }

    protected function applyProrationToPlanDetail(Order $order, $invoice, int $divisor = 100): void
    {
        $detail = $order->orderDetails()->first();
        if (! $detail || ! $detail->isVirtualAddress()) {
            return;
        }

        $planLines = [];
        if (isset($invoice->lines->data)) {
            foreach ($invoice->lines->data as $line) {
                if ($line->proration ?? false) {
                    $planLines[] = ((float) ($line->amount ?? 0)) / $divisor;
                }
            }
        }

        if (! empty($planLines)) {
            $netProrated = array_sum($planLines);
            $detail->update([
                'prorated_price' => round($netProrated, 2),
                'prorated_sub_total' => round($netProrated, 2),
                'proration_line_meta' => $planLines,
                'price' => round($netProrated, 2),
                'sub_total' => round($netProrated, 2),
            ]);

            return;
        }

        $amountDue = ((float) ($invoice->amount_due ?? 0)) / $divisor;
        $detail->update([
            'prorated_price' => round($amountDue, 2),
            'prorated_sub_total' => round($amountDue, 2),
            'price' => round($amountDue, 2),
            'sub_total' => round($amountDue, 2),
        ]);
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

    /**
     * Sync a subscription from Stripe for an order that was paid but missed the webhook.
     * Only syncs subscriptions with 'active' status that match the order's plan price IDs.
     *
     * @param Order $order
     * @return \App\Models\PlanSubscription|null The synced subscription or null if not found
     * @throws \Exception if Stripe API error occurs
     */
    public function syncSubscriptionFromStripe(Order $order): ?\App\Models\PlanSubscription
    {
        $order->load('user', 'orderDetails');
        $user = $order->user;

        // Find the virtual address order detail
        $vaDetail = $order->orderDetails
            ->where('product_type', ProductTypeEnum::VIRTUAL_ADDRESS->value)
            ->first();

        if (!$vaDetail || !$vaDetail->plan) {
            return null;
        }

        $planData = json_decode($vaDetail->plan, true);
        if (!$planData || !isset($planData['id'])) {
            return null;
        }

        $plan = Plan::find($planData['id']);
        if (!$plan) {
            return null;
        }

        // Get plan price IDs (monthly and yearly)
        $priceIds = array_filter([$plan->stripe_price_id_monthly, $plan->stripe_price_id_yearly]);
        if (empty($priceIds)) {
            return null;
        }

        // Get Stripe customer ID - use user's stripe_id or search by email
        $customerId = $user->stripe_id;
        if (!$customerId) {
            try {
                $stripe = \Laravel\Cashier\Cashier::stripe();
                $result = $stripe->customers->search([
                    'query' => "email:'{$user->email}'",
                ]);
                $customerId = $result->data[0]->id ?? null;
                if ($customerId) {
                    $user->update(['stripe_id' => $customerId]);
                }
            } catch (\Exception $e) {
                \Log::warning('OrderService: Could not find Stripe customer by email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        }

        if (!$customerId) {
            return null;
        }

        try {
            // Query Stripe for active subscriptions
            $stripe = \Laravel\Cashier\Cashier::stripe();
            $subscriptions = $stripe->subscriptions->all([
                'customer' => $customerId,
                'status' => 'active',  // Only active subscriptions
                'limit' => 100,
                'expand' => ['data.items.data.price'],
            ]);

            // Find subscription with matching plan price
            foreach ($subscriptions->data as $stripeSub) {
                foreach ($stripeSub->items->data as $item) {
                    if (in_array($item->price->id, $priceIds, true)) {
                        // Found matching subscription - sync it locally
                        return $this->createLocalSubscriptionFromStripe($user, $stripeSub, $plan);
                    }
                }
            }

            return null;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            \Log::error('OrderService: Stripe API error syncing subscription', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Failed to sync subscription from Stripe: ' . $e->getMessage());
        }
    }

    /**
     * Create/update local subscription and items from Stripe subscription object.
     * Mirrors Cashier's WebhookController::handleCustomerSubscriptionCreated pattern.
     *
     * @param User $user
     * @param \Stripe\Subscription $stripeSub
     * @param Plan $plan
     * @return \App\Models\PlanSubscription
     */
    protected function createLocalSubscriptionFromStripe(User $user, \Stripe\Subscription $stripeSub, Plan $plan): \App\Models\PlanSubscription
    {
        // Find or create local subscription
        $subscription = $user->subscriptions()->firstOrNew(['stripe_id' => $stripeSub->id]);

        $isSinglePrice = count($stripeSub->items->data) === 1;
        $firstItem = $stripeSub->items->data[0] ?? null;

        $subscription->type = 'default';
        $subscription->stripe_id = $stripeSub->id;
        $subscription->stripe_status = $stripeSub->status;
        $subscription->stripe_price = $isSinglePrice ? ($firstItem['price']['id'] ?? null) : null;
        $subscription->quantity = $isSinglePrice && isset($firstItem['quantity']) ? $firstItem['quantity'] : null;

        if (isset($stripeSub->trial_end)) {
            $subscription->trial_ends_at = \Carbon\Carbon::createFromTimestamp($stripeSub->trial_end);
        }

        $subscription->save();

        // Sync subscription items (plan, PSC, metered mail add-ons)
        foreach ($stripeSub->items->data as $item) {
            SubscriptionItem::updateOrCreate(
                ['subscription_id' => $subscription->id, 'stripe_id' => $item->id],
                [
                    'stripe_product' => $item['price']['product'] ?? null,
                    'stripe_price' => $item['price']['id'],
                    'quantity' => $item['quantity'] ?? null,
                ]
            );
        }

        return $subscription;
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