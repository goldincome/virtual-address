<?php

namespace App\Services\PaymentGateways;

use App\Enums\MailTypeEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Interfaces\PaymentGatewayInterface;
use App\Services\CartService;
use Exception;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeGateway implements PaymentGatewayInterface
{
    /**
     * Creates a Stripe Checkout session for either a subscription or a one-time payment.
     *
     * @param array $paymentData
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function charge($paymentData)
    {
        try { 
            $user = auth()->user();
            $cartService = app(CartService::class);
            // This method should check the cart and return the plan model if it exists.
            $plan = $cartService->getPlanFromCart();
            // get the mail price setting from the cart
            $mailPriceSetting = $cartService->getMailPriceSettingFromCart();
            
            // Define the redirect URLs. Add the session_id placeholder for success.
            $returnUrls = [
                'success_url' => route('stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancelled'),
            ];
            
            // --- SCENARIO 1: The cart contains a subscription plan. ---
            if ($plan) { 
                // Determine the correct price ID for the subscription interval.
                if($paymentData['plan']['subscription_type'] === SubscriptionTypeEnum::YEARLY->value){
                    $planPriceId = $paymentData['plan']['stripe_price_id_yearly'];
                    // Create a new subscription for the user.      
                    $checkout = $user->newSubscription('default', $planPriceId);
                    if($mailPriceSetting) {
                        // Add metered prices if they exist in cart
                        $checkout = $checkout->withMetadata([
                            'order_no' => $paymentData['order_no'],
                            'setup_monthly_metered_plan' => 'true',
                            'monthly_metered_price_id' => $mailPriceSetting['stripe_price_id']]
                        );
                    }
                    else{
                        $checkout = $checkout->withMetadata([
                            'order_no' => $paymentData['order_no'],
                            'setup_monthly_metered_plan' => 'false'
                            ]
                        );
                    }
                }
                else{
                    //dd($paymentData, $plan, $mailPriceSetting );
                    $planPriceId = $paymentData['plan']['stripe_price_id_monthly'];
                    $checkout = $user->newSubscription('default', $planPriceId);
                    if($mailPriceSetting){
                        // Add metered prices if they exist in cart
                        $checkout = $checkout->meteredPrice($mailPriceSetting['stripe_price_id']);
                    }
                    $checkout = $checkout->withMetadata(['order_no' => $paymentData['order_no']]);
                }
                // Check if there are also one-time products in the cart to add to the first invoice.
                $oneTimeItems = $this->getOneTimeItemsFromCart();
                $checkoutItems = !empty($oneTimeItems)
                    ? array_merge($returnUrls, ['line_items' => $oneTimeItems])
                    : $returnUrls;

                $session = $checkout->checkout($checkoutItems);

                return redirect()->away($session->url)->send();
            }

            // --- SCENARIO 2: The cart contains ONLY non-subscription products. ---
            $lineItems = $this->getOneTimeItemsFromCart();
            dd($lineItems);
            exit;
            if (!empty($lineItems)) {
                Stripe::setApiKey(config('cashier.secret'));
                $orderNo = $cartService->getOrderNoFromCartItem();
                $session = Session::create([
                    'payment_method_types' => ['card'],
                    'line_items'           => $lineItems,
                    'customer_email'       => $user->email,
                    // Use client_reference_id to link the session to your internal user ID.
                    'client_reference_id'  => $user->id,
                    'payment_intent_data' => [
                        'metadata' => ['order_no' => $orderNo]
                    ],
                    'mode'                 => 'payment', // Use 'payment' mode for one-time charges.
                    'success_url'          => $returnUrls['success_url'],
                    'cancel_url'           => $returnUrls['cancel_url'],
                ]);
               
                return redirect()->away($session->url)->send();
            }

            // If the cart is empty, throw an exception.
            throw new Exception("No items found in the cart to process.");

        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new Exception('Stripe API Error: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Verifies the Stripe session after a successful redirect.
     *
     * @param Request $request
     * @return \Stripe\Checkout\Session|false
     */
    public function paymentSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return false;
        }

        try {
            Stripe::setApiKey(config('cashier.secret'));
            $session = Session::retrieve($sessionId);
            
            // Verify the payment was successful.
            if ($session->payment_status === 'paid') {
                return $session;
            }

            return false;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    /**
     * Helper function to get all non-subscription items from the cart, formatted for Stripe.
     *
     * @return array
     */
    protected function getOneTimeItemsFromCart(): array
    {
        $lineItems = [];
        foreach (Cart::content() as $item) {
            // Assuming VIRTUAL_ADDRESS is your subscription product type.
            if ($item->options->type !== ProductTypeEnum::VIRTUAL_ADDRESS->value) {
                $lineItems[] = [
                    'price_data' => [
                        'currency'     => strtolower(config('cashier.currency', 'gbp')),
                        'product_data' => [
                            'name' => $item->name,
                            'description' => $item->options->description ?? $item->name,
                        ],
                        'unit_amount'  => (int)($item->price * 100),
                    ],
                    'quantity'   => $item->qty,
                ];
            }
        }

        // Add Tax/VAT if applicable.
        if (config('cart.tax') > 0 && Cart::tax() > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency'     => strtolower(config('cashier.currency', 'gbp')),
                    'product_data' => [
                        'name' => 'VAT (' . config('cart.tax') . "%)",
                    ],
                    'unit_amount'  => (int)(Cart::tax() * 100),
                ],
                'quantity'   => 1,
            ];
        }

        return $lineItems;
    }
}
