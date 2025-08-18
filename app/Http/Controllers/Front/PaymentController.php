<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Services\CartService;
use App\Services\OrderService;
use App\Enums\PaymentMethodEnum;
use App\Services\PaymentService;
use App\Http\Controllers\Controller;
use App\Services\UserBillingService;
use Gloudemans\Shoppingcart\Facades\Cart;


class PaymentController extends Controller
{
       protected $paymentService;
       protected $cartService;

    public function __construct(PaymentService $paymentService, CartService $cartService)
    {
        $this->paymentService = $paymentService;
        $this->cartService = $cartService;
    }

    /**
     * Process the payment based on the selected gateway.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processPayment(Request $request)
    {  
        // Validate the request
        $request->validate([
            'payment_method' => 'required|in:' . implode(',', PaymentMethodEnum::toArray()),
            'company_name' => 'nullable|string|max:255',
            'billing_address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'vat_no' => 'nullable|string|max:50',
        ]);
        
        if($request->payment_method === PaymentMethodEnum::DirectDebit->value)
        {
            $request->validate([
                'acct_holder_name' => 'required|string|max:255',
                'acct_number' => 'required|digits_between:8,10',
                'sort_code' => 'required|max:255',
                'approval_required' => 'nullable|boolean',
            ]);
        }
    
        $description = '';
        $paymentMethod = $request->payment_method;
        $orderNo = generateOrderNumber();
        Cart::search(function ($cartItem, $rowId) use ($orderNo, $paymentMethod, $description) {
            $updateData = [ 
            'order_no' => $orderNo,
            'payment_method' => $paymentMethod,
            ];
            // Update the cart item with the order number and payment method
            app(CartService::class)->updateCartItem($cartItem, $updateData);
            $description = $description. ' - ' .$cartItem->options->description; 
        });
        
        //Create or update user billing information
        app(UserBillingService::class)->createOrUpdate($request->except(['payment_method']));
        
        try {
            // Prepare payment data
            $paymentData = [
                'amount' => Cart::total(),
                'tax' => Cart::tax(),
                'description' => $description,
                'currency' => 'GBP',
                'quantity' => Cart::count(),
                'user_id' => auth()->id(), // Assuming user is authenticated
                'plan' => $this->cartService->getPlanFromCart() ?? null,
                'requestData' => $request->all(),
                'order_no' => $orderNo,
                // Add more data as needed (e.g., card details, description)
            ];
            
            // Resolve the payment gateway dynamically based on payment_type
            $this->paymentService->setPaymentGateway($request->payment_method);
 
            // Process the payment
            $result = $this->paymentService->execute($paymentData);
            //dd($paymentData);
            //check if payment was Direct Debit
            if($request->payment_method === PaymentMethodEnum::DirectDebit->value)
            {
                $order = $this->directDebitSuccess($request, app(OrderService::class));
                if ($order) {
                    return redirect()->route('checkout.success', $order->order_no)->with('success', 'Payment successful!');
                } else {
                    return redirect()->route('cart.index')->with('error', 'Payment not successful');
                }
            }
        } catch (\Exception $e) {
            //dd($e->getMessage());
            return redirect()->route('cart.index')->with('error','Payment processing failed, Something went wrong'. $e->getMessage());
        }
    }

     public function directDebitSuccess(Request $request, OrderService $orderService)
    {
        try {
            $paymentMethod = $this->cartService->getPaymentMethodFromCart();
            if ($paymentMethod === PaymentMethodEnum::DirectDebit->value) {
                $request['payment_method'] = $paymentMethod;
                $order = $orderService->createOrder($request->all());
                Cart::destroy();
               return $order;
            }
            return null; // or handle the case where payment method is not Direct Debit
        } catch (\Exception $e) {
            //dd($e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Payment confirmation failed: ');
        }
    }

    public function paypalSuccess(Request $request, OrderService $orderService)
    { 
        try {
           
            $paymentMethod = $this->cartService->getPaymentMethodFromCart();
            if ($request->has('token') && $paymentMethod === PaymentMethodEnum::PayPal->value) { 
                $paymentGateway = $this->paymentService->resolvePaymentGateway(PaymentMethodEnum::PayPal->value);
                $result = $paymentGateway->paymentSuccess($request);

                if ($result && $result['status'] === 'COMPLETED') {
                    $request['payment_method'] = PaymentMethodEnum::PayPal->value;
                    $order = $orderService->createOrder($request->all());
                    Cart::destroy();
                    return redirect()->route('checkout.success', $order->order_no)->with('success', 'Payment successful!');
                }
            }
            return redirect()->route('cart.index')->with('error', 'Payment not successful');
        } catch (\Exception $e) {
            //dd($e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Payment confirmation failed: ');
        }
    }

    public function stripeSuccess(Request $request, OrderService $orderService)
    {   
        try {
            // Determine which payment gateway was used.
            $paymentMethod = $this->cartService->getPaymentMethodFromCart();

            $orderCreated = false;
            //dd(Cart::content(), $paymentMethod);
            // Handle Stripe Success
            if ($paymentMethod === PaymentMethodEnum::Stripe->value) {
                $stripeGateway = $this->paymentService->resolvePaymentGateway(PaymentMethodEnum::Stripe->value);
                $session = $stripeGateway->paymentSuccess($request);
                
                if ($session) {
                    // Payment was successful, create the order.
                    $request['payment_method'] = $paymentMethod;
                    $request['payment_method_order_id'] = $session->id; // Store session ID for reference
                    $order = $orderService->createOrder($request->all());
                    $orderCreated = true;
                }
            }
          
            if ($orderCreated) {
                Cart::destroy();
                return redirect()->route('checkout.success', $order->order_no)->with('success', 'Payment successful!');
            }
        } catch (\Exception $e) {
        
            //dd($e->getMessage());
            return redirect()->route('cart.index')->with('error', 'Payment confirmation failed: ');
        }
    }

     public function paymentCancelled(Request $request)
    {
        //dd($request->all());
        return redirect()->route('cart.index')->with('error','Payment was cancelled.');
    }

}
