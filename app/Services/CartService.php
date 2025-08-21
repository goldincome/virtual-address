<?php
namespace App\Services;

use App\Models\Plan;
use App\Models\{Product};
use App\Models\MailSetting;
use Illuminate\Http\Request;
use App\Enums\ProductTypeEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\SubscriptionTypeEnum;
use Gloudemans\Shoppingcart\CartItem;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartService
{

    public function addVirtualAddressToCart(Plan $plan, Product $product)
    {
        if ($product->type->value === ProductTypeEnum::VIRTUAL_ADDRESS->value) {
            $prodFeatures = [];
            $features = $plan->features()->with('featureSetting')->select('id','feature_setting_id')->get();
            foreach($features as $feature){
                $prodFeatures[] = $feature->featureSetting()->select('id', 'name')->first()->toArray();
            }
            
            //'features' => $prodFeatures ? json_encode($prodFeatures) : null,
            // Remove any existing virtual address plan
            foreach (Cart::content() as $item) {
                if (isset($item->options['type']) && $item->options['type'] === ProductTypeEnum::VIRTUAL_ADDRESS->value) {
                    Cart::remove($item->rowId);
                }
            }
            $quantity = 1;
            // Set the price based on the subscription type
            $price = $plan->price;
            // If the plan is yearly, calculate the monthly price
            if ($plan->subscription_type === SubscriptionTypeEnum::YEARLY->value) {
                $price =  $plan->yearly_monthly_price;
            }
            // If the plan has metered mail services, retrieve the specific mail setting
            if(!$plan->mailSettings->isEmpty()){
                $mailSetting = MailSetting::where('plan_id', $plan->id)
                ->where('mail_type', $plan->mail_type)
                ->where('status', true)
                ->firstOrFail();
            }
            
            //dd($mailSetting, MailTypeEnum::tryFrom($plan->mail_type));
            Cart::add(
                'va_plan_' . $plan->id, // Unique ID for this specific plan as a cart item
                $product->name . ' - ' . $plan->name,
                $quantity, // Quantity is always 1 for virtual address plans
                $price,
                0, // No weight
                [ // Options
                    'type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
                    'order_no' => '',
                    'tax' => (config('cart.tax')/100) * ($price * $quantity),
                    'product_model_id' => $product->id, // Original product model ID
                    'plan_id' => $plan->id,
                    'subscription_type' => $plan->subscription_type ?? null,
                    'mail_type' => $mailSetting->mail_type ?? null,
                    'mail_price_setting' => $mailSetting ?? null,
                    'plan' => $plan ? $plan : null,
                    'stripe_price_id' => $plan->subscription_type === SubscriptionTypeEnum::YEARLY->value ? $plan->stripe_price_id_yearly : $plan->stripe_price_id_monthly,
                    'features' => $prodFeatures ?? null,
                    'description' => $product->intro ?: $product->intro,
                    'image' => $product->main_product_image ?: 'https://placehold.co/100x80/1e4ed8/ffffff?text=V-Office',
                ]
            );
            // Update the room price if the cart has a plan and room
            $this->updateRoomPriceIfCartHasPlanAndRoom();
            return true;
        }
        return false;
    }

    public function addRoomToCart(Product $product, string $productType, array $cartData)
    {   //dd($product->type->value, $productType);
        Cart::instance('default');
        if ($product->type->value === $productType) {
            //$product = Product::findOrFail($productIdInput); //
            //$quantity = $cartData['quantity'] ?? 1; // Number of hours
            $bookingDate = $cartData['booking_date'];
            $bookingTimes = $cartData['booking_time'];
            $cartItemId = 'room_' . $product->id . '_' . strtotime($bookingDate);  
            $qty = 0;   
            $bookingTimeNew = $bookingTimeNewDisplay = [];
            // get product features
            $prodFeatures = [];
            $features = $product->features()->with('featureSetting')->select('id','feature_setting_id')->get();
            foreach($features as $feature){
                $prodFeatures[] = $feature->featureSetting()->select('id', 'name')->first()->toArray();
            }

            $existingItem = Cart::search(function ($cartItem, $rowId) use ($cartItemId, $bookingDate) {
                    return ($cartItem->id === $cartItemId && 
                        //$cartItem->options['booking_time_raw'] === $bookingTimeRaw &&
                         $cartItem->options['booking_date'] === $bookingDate
                    );
                });
            if($existingItem->isEmpty())
            {
                foreach ($bookingTimes as $bookingTimeRaw) {
                    $bookingTimeNew[] = $bookingTimeRaw;
                    $bookingTimeNewDisplay[] = $this->formatBookingTime($bookingTimeRaw);
                    ++$qty;
                }
                //dd($bookingTimeNew, $bookingTimeNewDisplay);
                $quantity = $qty ?? 1;
                //if (!$existingItem->isNotEmpty()) {
                Cart::add(
                    $cartItemId, // Unique ID for this booking slot
                    $product->name,
                    $quantity, // Hours
                    $product->price, // Price per hour
                    0, // No weight
                    [ // Options
                        'type' => $productType,
                        'order_no' => '',
                        'tax' => (config('cart.tax')/100) * ($product->price * $quantity),
                        'product_model_id' => $product->id, // Original product model ID
                        'booking_date' => $bookingDate,
                        'booking_time_raw' => $bookingTimeNew ?? $bookingTimeRaw,
                        'booking_time_display' => $bookingTimeNewDisplay ?? $this->formatBookingTime($bookingTimeRaw),
                        'description' => $product->intro,
                        'features' => $prodFeatures ?? null,
                        'image' => $product->type->value === ProductTypeEnum::CONFERENCE_ROOM->value ? $product->conference_primary_image : $product->meeting_primary_image, //
                    ]
                );
                // Update the room price if the cart has a plan and room
                $this->updateRoomPriceIfCartHasPlanAndRoom();
                return true; 
            }
            
            if ($existingItem->isNotEmpty()) {
                $existingItem = Cart::search(function ($cartItem, $rowId) use ($cartItemId, 
                $bookingDate, $bookingTimes, $product, $productType, $qty) 
                {
                    if ($cartItem->id === $cartItemId && 
                         $cartItem->options['booking_date'] === $bookingDate
                    )
                    { //dd($bookingTimes);
                        foreach ($bookingTimes as $bookingTimeRaw) {
                            $bookingTimeNew[] = $bookingTimeRaw;
                            $bookingTimeNewDisplay[] = $this->formatBookingTime($bookingTimeRaw);
                            ++$qty;
                        }
                        //dd(array_unique(array_merge($cartItem->options->booking_time_raw, $bookingTimes)), $bookingTimes, $cartItem->options->booking_time_raw);
                        $bookingTimeNew = array_unique(array_merge($cartItem->options->booking_time_raw, $bookingTimeNew));
                        $bookingTimeNewDisplay = array_unique(array_merge($cartItem->options->booking_time_display,$bookingTimeNewDisplay));
                        $quantity = $cartItem->qty + $qty;
                        //dd($cartItem, $bookingTimeNew,$bookingTimeNewDisplay, $quantity);
                        Cart::remove($rowId);
                        Cart::add(
                                    $cartItemId, // Unique ID for this booking slot
                                    $product->name,
                                    $quantity, // Hours
                                    $product->price, // Price per hour
                                    0, // No weight
                                    [ // Options
                                        'type' => $productType,
                                        'order_no' => '',
                                        'tax' => (config('cart.tax')/100) * ($product->price * $quantity),
                                        'product_model_id' => $product->id, // Original product model ID
                                        'booking_date' => $bookingDate,
                                        'booking_time_raw' => $bookingTimeNew ?? $bookingTimeRaw,
                                        'booking_time_display' => $bookingTimeNewDisplay ?? $this->formatBookingTime($bookingTimeRaw),
                                        'description' => $product->intro,
                                        'features' => $prodFeatures ?? null,
                                        'image' => $product->type->value === ProductTypeEnum::CONFERENCE_ROOM->value ? $product->conference_primary_image : $product->meeting_primary_image, //
                                    ]
                        );
                        // Update the room price if the cart has a plan and room
                        $this->updateRoomPriceIfCartHasPlanAndRoom();
                        return true;
                    }
                });
                
            }
        }
        return false;
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
    public function remove($rowId)
    {
        try {
            Cart::remove($rowId);
            $this->resetRoomPriceToDefaultIfCartHasNoPlan(); // Update room prices if necessary
            return true;     
        } catch (\Exception $e) {
            throw new \Exception('Error removing item from cart: ' . $e->getMessage());
        }
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        Cart::destroy();
        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully.');
    }

    /**
     * Helper to format booking time for display.
     * Example input: "0900-1000" -> "9:00 AM - 10:00 AM"
     */
    private function formatBookingTime($timeSlot)
    {
        if (preg_match('/(\d{2})(\d{2})-(\d{2})(\d{2})/', $timeSlot, $matches)) {
            $startTime = \DateTime::createFromFormat('Hi', $matches[1] . $matches[2])->format('g:i A');
            $endTime = \DateTime::createFromFormat('Hi', $matches[3] . $matches[4])->format('g:i A');
            return $startTime . ' - ' . $endTime;
        }
        return $timeSlot; // Return original if not in expected format
    }

    public function getOrderNoFromCartItem()
    {
        foreach (Cart::content() as $item) {
            if (isset($item->options['order_no'])) {
                return $item->options['order_no'];
            }
        }
    }
    // Get CartItem data to up
    public function updateCartItem(CartItem $item, array $updateData,  ?float $price = null)
    {
        // If a price is provided, update the price in the options
        if ($price !== null) {
            $updateData = [
                'price' => $price,
                'options' => array_merge($item->options->toArray(), $updateData),
            ];
        }
        else
        {
            $updateData = ['options' => array_merge($item->options->toArray(),$updateData)];  
        }
        Cart::update($item->rowId, $updateData);
    }
    // Get the plan from the cart items
    // This method is used to retrieve the plan associated with the virtual address in the cart
    public function getPlanFromCart()
    {
        foreach (Cart::content() as $item) {
            if ($item->options->type === ProductTypeEnum::VIRTUAL_ADDRESS->value) {
                return $item->options->plan;
                exit;
            }
        }
        return null;
    }

    // Get the mail price setting from the cart items
    // This method is used to retrieve the mail price setting associated with the virtual address in the
    public function getMailPriceSettingFromCart()
    {
        foreach (Cart::content() as $item) {
            if ($item->options->type === ProductTypeEnum::VIRTUAL_ADDRESS->value) {
                return $item->options->mail_price_setting ?? null;
            }
        }
        return null;
    }
    // Get the payment method from the cart items
    // This method is used to determine the payment method from checkout
    public function getPaymentMethodFromCart()
    { 
        foreach (Cart::content() as $item) {
            if (in_array($item->options->payment_method, PaymentMethodEnum::toArray())) {
                    return $item->options->payment_method;
            }
        }
        return null;
    }

    //check if it subscription or ordinary product or both is in the cart
    public function checkProductTypeInTheCart()
    {
        $output = [];
        $output['virtual'] = false;
        $output['room'] = false;
       foreach(Cart::content() as $cartItem) {
            if($cartItem->options->type === ProductTypeEnum::VIRTUAL_ADDRESS->value)
            {   
                $output['virtual'] =  true;
            }
            if($cartItem->options->type === ProductTypeEnum::CONFERENCE_ROOM->value
                || $cartItem->options->type === ProductTypeEnum::MEETING_ROOM->value)
            { 
                $output['room'] = true;
            }
        };
        return $output;
    }
    // Additional method to add other items to Stripe payment from cart
    // This method is used to prepare data for Stripe payment
    public function addOtherItemsToStripePaymentFromCart()
    {
        $data = [];
        $data['name'] = $data['description'] = '';
        $data['total'] = 0;
        foreach(Cart::content() as $cartItem){
            if($cartItem->options->type !== ProductTypeEnum::VIRTUAL_ADDRESS->value){
               $data['name'] = $data['name'] . $cartItem->name .' '.$cartItem->qty.' hrs '. '- ';
               $data['description'] = $data['description']. 'Booked: '. $cartItem->name.' on '.$cartItem->options->booking_date.' for '.$cartItem->qty.' hrs, ' ;
               $data['total'] = $data['total'] + $cartItem->total();
               $data['currency'] = config('cashier.currency');
            }
        }
        return $data;
    }
   
    public function checkIfCartHasVirtualAddress()
    {
        foreach (Cart::content() as $item) {
            if ($item->options->type === ProductTypeEnum::VIRTUAL_ADDRESS->value) {
                return true;
            }
        }
        return false;
    }

    // Apply discount to rooms if the cart has both a plan and a room
    public function updateRoomPriceIfCartHasPlanAndRoom()
    {
        $check = $this->checkProductTypeInTheCart();
        if (($check['virtual'] &&  $check['room']) || (auth()->user() && auth()->user()->subscribed('default'))) {
            //check if subscribed to a plan that qualifies for discount
            if(auth()->user() && auth()->user()->subscribed('default')) // user has a plan 
            {
                $plan =  auth()->user()->subscription('default')->plan;
                //dd('I am here', auth()->user()->subscribed('default'),  $plan);
            }
            else{ //if cart has a plan that qualifies for discount
                $plan = $this->getPlanFromCart();
                $plan = Plan::where('id', $plan['id'])->first();
            }
            foreach (Cart::content() as $item) {
                if($item->options->type === ProductTypeEnum::CONFERENCE_ROOM->value
                    || $item->options->type === ProductTypeEnum::MEETING_ROOM->value)
                { 
                    $product = Product::where('id', $item->options->product_model_id)->first();
                    $discount = app(PlanRoomDiscountService::class)->getDiscountsByPlanAndProductId($plan, $product);
                    
                    if(!empty($discount)){
                        // Update the cart item with the discounted price and options
                       $updateData =  [
                                'discounted_price' => $discount['discounted_price'],
                                'discount_amount' => $discount['discount_amount'],
                                'discount_value' => $discount['discount_value'],
                                'product_price' => $discount['product_price'],
                                'discount_type' => $discount['discount_type'],
                       ];
                       $updateData['all_discount_data'] = $updateData ;
                        $this->updateCartItem($item, $updateData , $discount['discounted_price']);
                    }
                }  
            }
        };
        return [];
    }

    // Reset room prices to default if no plan is found in the cart
    // This is useful when a user removes a plan from the cart
    public function resetRoomPriceToDefaultIfCartHasNoPlan()
    {
        $plan = $this->getPlanFromCart();
        if(auth()->user() && auth()->user()->subscribed('default')) // user has a plan 
        {
            $plan =  auth()->user()->subscription('default')->plan ?? null;
        }
        if ($plan === null) {
            // Reset room prices to default if no plan is found in the cart    
            foreach (Cart::content() as $item) {
                if($item->options->type === ProductTypeEnum::CONFERENCE_ROOM->value
                    || $item->options->type === ProductTypeEnum::MEETING_ROOM->value)
                { 
                    $product = Product::where('id', $item->options->product_model_id)->first();
                    // Reset the price to the original product price
                    // and remove any discount options
                    $updateData = [
                            'discounted_price' => 0.00,
                            'discount_amount' => 0.00,
                            'discount_value' => '',
                            'product_price' => $product->price,
                            'discount_type' => '',
                    ];
                    $updateData['all_discount_data'] = $updateData ;
                    Cart::update($item->rowId, [
                        'price' => $product->price,
                        'options' => array_merge($item->options->toArray(), $updateData),
                    ]);
                    
                }  
            }
        };
        return [];
    }

}
