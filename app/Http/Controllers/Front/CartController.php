<?php

namespace App\Http\Controllers\Front;

use App\Models\Order;
use App\Mail\NewOrderEmail;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Enums\ProductTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Actions\CalculateCartTotalDiscount;
use App\Actions\FilterSubscribedOrLowerPlansFromCart;

class CartController extends Controller
{
    protected $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
        
    }
     /**
     * Display the shopping cart.
     */
    public function index()
    {
        app(FilterSubscribedOrLowerPlansFromCart::class)->execute();
        $cartItems = Cart::content();
        $productType = ProductTypeEnum::class;
        $subscriptionTypes = SubscriptionTypeEnum::class;
        return view('front.cart.index', compact('cartItems','productType', 'subscriptionTypes'));
    }

    /**
     * Add an item to the cart.
     */
    
    public function checkout()
    {
        if(Cart::count() == 0){
            return to_route('cart.index');
        }
        try {
           $cartItems = Cart::content();
           $productType =  ProductTypeEnum::class;
           $paymentMethods = PaymentMethodEnum::class;
            // Create a Stripe Setup Intent
            $intent = auth()->user()->createSetupIntent();
           $isSubscription = app(CartService::class)->checkIfCartHasVirtualAddress();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error no product to checkout: ' . $e->getMessage())->withInput();
        }

        return view('front.checkout.index', compact('cartItems', 'intent', 'productType', 'paymentMethods', 'isSubscription'));
    }
    

    /**
     * Update cart item quantity (primarily for rooms).
     */
    public function update(Request $request)
    {
        $request->validate([
            'rowId' => 'required|string',
            'quantity' => 'required|integer|min:1',
        ]);

        $rowId = $request->input('rowId');
        $item = Cart::get($rowId);

        if (!$item) {
            return redirect()->route('cart.index')->with('error', 'Item not found in cart.');
        }

        // Virtual addresses quantity cannot be updated.
        if (isset($item->options['type']) && $item->options['type'] === 'virtual_address') {
            return redirect()->route('cart.index')->with('warning', 'Virtual address plan quantity cannot be changed.');
        }

        Cart::update($rowId, $request->quantity);
        return redirect()->route('cart.index')->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request)
    {
        $request->validate(['rowId' => 'required']); // Cart item ID
        app(CartService::class)->remove($request->rowId);
        return redirect()->route('cart.index')->with('success', 'Item removed from cart.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        Cart::destroy();
        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully.');
    }

    public function checkoutSuccess(string $order_no)
    {   
        try{
            $user = auth()->user();
            $order = $user->orders()->where('order_no', $order_no)->where('user_id', auth()->id())->firstOrFail();
            $order->load('orderDetails', 'orderDetails.myPlan', 'orderDetails.product', 'user');
            $jsonPlan = null;
            if($order->hasSubscription()){
                $jsonPlan = json_decode($user->orderDetails()->where('product_type', ProductTypeEnum::VIRTUAL_ADDRESS->value)->latest()->first()->plan);
            }
            $subscriptionType = SubscriptionTypeEnum::class;
            
            Mail::to('bodypal4me@gmail.com')->queue(new NewOrderEmail($order));
            //return view('emails.CustomerOrderEmail', compact('order','jsonPlan','subscriptionType'));
            return view('front.checkout.success',compact('order','jsonPlan','subscriptionType'));
        } catch (\Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Order not found or an error occurred: ' . $e->getMessage());
        }
    }
}
