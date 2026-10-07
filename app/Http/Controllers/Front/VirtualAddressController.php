<?php

namespace App\Http\Controllers\Front;

use App\Models\Plan;
use App\Models\Product;
use App\Models\PscType;
use App\Enums\MailTypeEnum;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Enums\ProductTypeEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Http\Controllers\Controller;

class VirtualAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $virtualAddress = app(Product::class)->virtual_address;
        $plans = $virtualAddress->plans()->with(['features', 'media', 'features.featureSetting'])
            ->where('is_active', true)->get();

        $planGroups = Plan::featureGroupsForListing($plans);

        return view('front.virtual-address.index', compact('plans', 'planGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CartService $cartService)
    {  
        $request->validate([
            'subscription_type' => 'required|string|in:'.implode(',', SubscriptionTypeEnum::getValues()) . ',',
            'mail_type' => 'nullable|string|in:'.implode(',', MailTypeEnum::toArray()) . ',',
            'psc' => 'nullable|array',
            'psc.*' => 'nullable|integer|min:0|max:100',
        ]);

        $plan = Plan::select('id','name','price', 'yearly_monthly_price', 'product_id', 'stripe_price_id_yearly', 'stripe_price_id_monthly','payment_price_id')->where('id', $request->plan_id)->first();
        if (!$plan) {
            return redirect()->back()->with('error', 'Virtual Address plan not found.')->withInput();
        }
        $plan['subscription_type'] = $request->subscription_type;
        
        $product = $plan->product;
        if($plan->mailSettings->isNotEmpty()) {
            $plan['mail_type'] = $request->mail_type;
        } 

        $pscQuantities = is_array($request->input('psc')) ? $request->input('psc') : [];
        if (!empty(array_filter($pscQuantities))) {
            if (!$plan->allowsCompanyPsc()) {
                return redirect()->back()->with('error', 'This plan does not include Company Person with Significant Control.')->withInput();
            }
        }

        try{
            $cartService->addVirtualAddressToCart($plan, $product, $pscQuantities);
        } catch (\Exception $e) {
            // Log::error('Error adding product to cart: ' . $e->getMessage()); // Good practice to log
            return redirect()->back()->with('error', 'Error adding product to cart. Please try again.')->withInput();
        }
        return redirect()->route('cart.index')->with('success', 'Virtual Address-' . $plan->name . ' added to cart.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $plan = Plan::whereSlug($slug)->first();
        $plan->load(['features', 'media', 'features.featureSetting', 'mailSettings']);
        //dd($plan->mailSettings);
        $subscriptionTypes = SubscriptionTypeEnum::class;
        $mailTypes = MailTypeEnum::class;
        if (!$plan) {
            return redirect()->route('virtual-address.index')->with('error', 'Virtual Address plan not found.');
        }
        //check if user has subscription
        $subscription = auth()->user() ? (auth()->user()->subscription('default') ?? null) : null;
        $allPlans = Plan::select('id', 'slug', 'level', 'name')->where('is_active', true)->get();
        $pscTypes = PscType::where('status', true)->orderBy('id')->get();
        return view('front.virtual-address.show', compact('plan', 'allPlans', 'subscription', 'subscriptionTypes', 'mailTypes', 'pscTypes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Plan $plan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Plan $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Plan $plan)
    {
        //
    }
}
