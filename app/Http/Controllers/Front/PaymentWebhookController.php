<?php

namespace App\Http\Controllers\Front;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use App\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\Log;
use Stripe\Subscription as StripeSubscription;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierController;

class PaymentWebhookController extends CashierController
{
    /**
     * Handle customer subscription created.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleCustomerSubscriptionCreated(array $payload): Response
    {
        // First, let Cashier do its default job of creating the subscription record.
        $response = parent::handleCustomerSubscriptionCreated($payload);

        // Now, add your custom logic.
        try {
            $stripeCustomerId = $payload['data']['object']['customer'];
            $user = Cashier::findBillable($stripeCustomerId);

            if ($user) {
                $subscriptionData = $payload['data']['object'];
                $metadata = $subscriptionData['metadata'];
                if (isset($metadata['setup_monthly_metered_plan']) && $metadata['setup_monthly_metered_plan'] === 'true') {
                    if (!$user->subscribed('metered')) {
                        
                        $meteredPriceId = $metadata['monthly_metered_price_id'];

                        // Create the second, separate subscription for metered billing.
                        $user->newSubscription('metered', $meteredPriceId)
                             ->meteredPrice($meteredPriceId)
                             ->create();
                    }
                }
                if(isset($metadata['order_no'])){
                    $order = Order::where('order_no')->first();
                    if($order){
                        $order->update(['status' => PaymentStatusEnum::Paid->value]);
                    }
                }
                Log::info('Webhook: New subscription created.', [ 'user_id' => $user->id, 'payload' => $payload]);
                
                // Example: Assign a 'premium-member' role to the user or dispatch a job.
                // $user->assignRole('premium-member');
            } else {
                Log::warning('Webhook: Received subscription created event but could not find billable user.', ['stripe_customer' => $stripeCustomerId]);
            }
        } catch (\Exception $e) {
            Log::error('Webhook: Error in custom logic for handleCustomerSubscriptionCreated.', ['error' => $e->getMessage()]);
        }


        return $response;
    }

    /**
     * Handle invoice payment succeeded.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleInvoicePaymentSucceeded(array $payload): Response
    {
        // Add custom logic for successful payments here.
        // For example, fulfilling an order, sending a thank you email, etc.
        $stripeCustomerId = $payload['data']['object']['customer'];
        $user = Cashier::findBillable($stripeCustomerId);

        if ($user) {

                 // Extract order_no from the subscription metadata within the invoice payload.
                //$order = $this->updateOrderFromMetadata($payload);
        } 
        else {
            Log::warning('Webhook: Received Payment event but could not find billable user.', ['stripe_customer' => $stripeCustomerId]);
        }
        Log::info('Webhook: Invoice payment succeeded.', ['payload' => $payload]);

        return new Response('Webhook Handled', 200);
    }
     /**
     * Handle invoice payment failed.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleInvoicePaymentFailed(array $payload): Response
    {
        try {
            $stripeCustomerId = $payload['data']['object']['customer'];
            $user = Cashier::findBillable($stripeCustomerId);

            if ($user) {
                // Update order status if metadata exists
                $order = $this->updateOrderFromMetadata($payload, $user);
                if ($order) {
                    $order->update(['status' => PaymentStatusEnum::Failed->value]);
                }
                Log::warning('Webhook: Invoice payment failed.', [
                    'user_id' => $user->id,
                    //'order' => $order,
                    //'amount' => $payload['data']['object']['amount_due'],
                    'payload' => $payload
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Webhook: Error in handleInvoicePaymentFailed.', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
        }

        return new Response('Webhook Handled', 200);
    }

        /**
     * Handle customer subscription updated.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleCustomerSubscriptionUpdated(array $payload): Response
    {
        $response = parent::handleCustomerSubscriptionUpdated($payload);

        try {
            $stripeCustomerId = $payload['data']['object']['customer'];
            $user = Cashier::findBillable($stripeCustomerId);

            if ($user) {
                //$subscription = $this->updateOrderFromMetadata($payload, $user);
              // $subscription = $this->updateSubscriptionEndDate($payload, $user);

                Log::info('Webhook: Subscription updated.', [
                    'user_id' => $user->id,
                    //'subscription' => $subscription ?? null,
                    'payload' => $payload
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Webhook: Error in handleCustomerSubscriptionUpdated.', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
        }

        return $response;
    }

    /**
     * Handle customer subscription deleted (canceled).
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleCustomerSubscriptionDeleted(array $payload): Response
    {
        $response = parent::handleCustomerSubscriptionDeleted($payload);

        try {
            $stripeCustomerId = $payload['data']['object']['customer'];
            $user = Cashier::findBillable($stripeCustomerId);

            if ($user) {
              
                Log::info('Webhook: Subscription canceled.', [
                    'user_id' => $user->id,
                    'stripe_customer' => $stripeCustomerId,
                    'payload' => $payload
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Webhook: Error in handleCustomerSubscriptionDeleted.', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
        }

        return $response;
    }

    /**
     * Handle subscription expiration (when period ends).
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleCustomerSubscriptionExpired(array $payload): Response
    {
        try {
            $stripeCustomerId = $payload['data']['object']['customer'];
            $user = Cashier::findBillable($stripeCustomerId);

            if ($user) {
               
                Log::info('Webhook: Subscription expired.', [
                    'user_id' => $user->id,
                    'stripe_customer' => $stripeCustomerId
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Webhook: Error in handleCustomerSubscriptionExpired.', [
                'error' => $e->getMessage(),
                'payload' => $payload
            ]);
        }

        return new Response('Webhook Handled', 200);
    }

     /**
     * Update order status from Stripe metadata
     */
    protected function updateOrderFromMetadata(array $payload, $user)
    {
        /* Check multiple possible locations for metadata
        $metadata = $payload['data']['object']['parent']['subscription_details']['metadata'] ?? 
                   ($payload['data']['object']['lines']['data'][0]['metadata'] ?? 
                   ($payload['data']['object']['metadata'] ?? []));

        $orderNo = $metadata['order_no'] ?? null;

        if ($orderNo) {
            $order = Order::where('order_no', $orderNo)->first();
            if ($order) {
                $order->update(['status' => PaymentStatusEnum::Approved->value]);
                return $order;
            }
        }

        return null;
        */
        $data = $payload['data']['object']; 
        $subscription = $user->subscriptions()->where('stripe_id', $data['id'])->first();

        if($subscription){
            $endingAtDate = Carbon::createFromTimestamp($data['current_period_end']);
            $subscription->ending_at = $endingAtDate;
            $subscription->save(); 
        }
        return $subscription;
    }

    /*
     * Update subscription end date from Stripe payload
     
    protected function updateSubscriptionEndDate(array $payload, User $user)
    {
        $stripeSubscriptionId = $payload['data']['object']['parent']['subscription_details']['subscription'] ;
        
        $subscription = $user->subscriptions()
            ->where('stripe_id', $stripeSubscriptionId)
            ->first();

        if ($subscription) {
            $periodEnd = Carbon::createFromTimestamp(
                $payload['data']['object']['current_period_end']
            );
            $subscription->update(['ends_at' => $periodEnd]);
            return $subscription;
        }

        return null;
    }
      */  
}
