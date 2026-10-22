<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Enums\PaymentStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Services\OrderService;
use App\Services\SubscriptionChangeService;
use App\Jobs\SendPaymentReceiptEmail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        // Start the query with eager loaded relationships to prevent N+1 issues
        $query = Order::with('user', 'orderDetails')->latest();

        // If a search term is present, apply the search conditions
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                // Search in the Order table
                $q->where('order_no', 'like', '%' . $searchTerm . '%')
                 
                  // Search in the related User table
                  ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', '%' . $searchTerm . '%')
                                ->orWhere('email', 'like', '%' . $searchTerm . '%');
                                // ->orWhere('phone_number', 'like', '%' . $searchTerm . '%'); // Uncomment if you have a phone_number field
                    })
                 
                  // Search in the related OrderDetail table
                  ->orWhereHas('orderDetails', function ($detailsQuery) use ($searchTerm) {
                      $detailsQuery->where('ref_no', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Paginate the results and append the search query to pagination links
        $orders = $query->latest()->paginate(15)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load('orderDetails', 'user');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Approve a manual payment order and send the payment receipt.
     * For subscription orders, also handle subscription activation/upgrade.
     */
    public function approve(Order $order)
    {
        try {
            $order->load('user', 'orderDetails');

            // Check if this is a virtual address subscription order
            $virtualAddressDetail = $order->orderDetails
                ->where('product_type', ProductTypeEnum::VIRTUAL_ADDRESS->value)
                ->first();

            if ($virtualAddressDetail && $virtualAddressDetail->plan) {
                $planData = json_decode($virtualAddressDetail->plan, true);
                
                if ($planData && isset($planData['id'])) {
                    $plan = Plan::find($planData['id']);
                    
                    if ($plan) {
                        $user = $order->user;
                        $interval = $planData['subscription_type'] ?? SubscriptionTypeEnum::MONTHLY->value;
                        
                        // Check if user has an existing active subscription
                        $existingSubscription = $user->subscription('default');
                        
                        if ($existingSubscription && $existingSubscription->active() && $existingSubscription->plan) {
                            $currentPlan = $existingSubscription->plan;
                            
                            if ($currentPlan->id === $plan->id) {
                                // Same plan - just confirm payment
                                $this->orderService->confirmPayment($order);
                                
                                return back()->with('success', 'Order approved successfully. Payment receipt email has been queued. Subscription already active on this plan.');
                            }
                            
                            // Different plan - check if upgrade or downgrade
                            if ((int) $plan->level > (int) $currentPlan->level) {
                                // Upgrade
                                $result = app(SubscriptionChangeService::class)->changePlan($user, $plan, $interval);
                                
                                $message = 'Order approved and subscription upgraded to ' . $plan->name . ' successfully.';
                                if (!empty($result['redirect_url'])) {
                                    $message .= ' Additional payment required via Stripe hosted invoice.';
                                }
                                
                                $this->orderService->confirmPayment($order);
                                
                                return back()->with('success', $message);
                            } else {
                                // Downgrade
                                $result = app(SubscriptionChangeService::class)->changePlan($user, $plan, $interval);
                                
                                $this->orderService->confirmPayment($order);
                                
                                return back()->with('success', 'Order approved successfully. Subscription downgrade to ' . $plan->name . ' has been scheduled for the end of the billing period.');
                            }
                        } else {
                            // No existing subscription - try to sync from Stripe first
                            try {
                                $syncedSubscription = $this->orderService->syncSubscriptionFromStripe($order);
                                
                                if ($syncedSubscription) {
                                    // Subscription found and synced - re-evaluate with the now-existing subscription
                                    $existingSubscription = $syncedSubscription;
                                    
                                    // Re-run the logic for existing subscription (same plan / upgrade / downgrade)
                                    $currentPlan = $existingSubscription->plan;
                                    
                                    if ($currentPlan->id === $plan->id) {
                                        // Same plan - just confirm payment
                                        $this->orderService->confirmPayment($order);
                                        
                                        return back()->with('success', 'Order approved successfully. Payment receipt email has been queued. Subscription synced from Stripe and already active on this plan.');
                                    }
                                    
                                    // Different plan - check if upgrade or downgrade
                                    if ((int) $plan->level > (int) $currentPlan->level) {
                                        // Upgrade
                                        $result = app(SubscriptionChangeService::class)->changePlan($user, $plan, $interval);
                                        
                                        $message = 'Order approved and subscription upgraded to ' . $plan->name . ' successfully.';
                                        if (!empty($result['redirect_url'])) {
                                            $message .= ' Additional payment required via Stripe hosted invoice.';
                                        }
                                        
                                        $this->orderService->confirmPayment($order);
                                        
                                        return back()->with('success', $message);
                                    } else {
                                        // Downgrade
                                        $result = app(SubscriptionChangeService::class)->changePlan($user, $plan, $interval);
                                        
                                        $this->orderService->confirmPayment($order);
                                        
                                        return back()->with('success', 'Order approved successfully. Subscription downgrade to ' . $plan->name . ' has been scheduled for the end of the billing period.');
                                    }
                                } else {
                                    // No subscription found in Stripe either - just confirm payment
                                    $this->orderService->confirmPayment($order);
                                    
                                    return back()->with('success', 'Order approved successfully. Payment receipt email has been queued. No active Stripe subscription found to sync for this plan.');
                                }
                            } catch (\Exception $e) {
                                \Log::error('OrderController: Error syncing subscription from Stripe during approve', [
                                    'order_id' => $order->id,
                                    'user_id' => $user->id,
                                    'error' => $e->getMessage(),
                                ]);
                                
                                // Show error to admin but don't block the approve action entirely
                                return back()->with('error', 'Order approved but failed to sync subscription from Stripe: ' . $e->getMessage());
                            }
                        }
                    }
                }
            }

            // For non-subscription orders, just confirm payment
            $this->orderService->confirmPayment($order);
            SendPaymentReceiptEmail::dispatch($order);

            return back()->with('success', 'Order approved successfully. Payment receipt email has been queued.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve order: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a manual payment order.
     */
    public function cancel(Order $order)
    {
        try {
            $order->update(['status' => PaymentStatusEnum::Cancelled->value]);

            return back()->with('error', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
