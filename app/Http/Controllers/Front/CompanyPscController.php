<?php
namespace App\Http\Controllers\Front;

use App\Models\Company;
use App\Models\PscType;
use App\Models\UserBilling;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PscService;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;

class CompanyPscController extends Controller
{
    public function store(Request $request, Company $company)
    {
        abort_unless((int) $company->user_id === (int) auth()->id(), 403);

        $request->validate([
            'psc_type_id' => 'required|exists:psc_types,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
        ]);

        $pscType = PscType::where('status', true)->findOrFail($request->psc_type_id);

        try {
            app(PscService::class)->addPerson($company, $pscType, $request->all());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('companies.show', $company)
            ->with('success', 'Person with Significant Control added successfully.');
    }

    /**
     * Add the requested PSC allowance (top-up) quantities to the cart so the
     * user can pay for more slots through the normal checkout flow.
     */
    public function topUp(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->subscribed('default')) {
            return redirect()->back()->with('error', 'You need an active Virtual Address plan to purchase PSC slots.');
        }
        $plan = $user->subscription('default')->plan;
        if (!$plan || !$plan->allowsCompanyPsc()) {
            return redirect()->back()->with('error', 'Your current plan does not include Company Person with Significant Control.');
        }

        $request->validate([
            'psc' => 'required|array',
            'psc.*' => 'nullable|integer|min:0|max:100',
        ]);

        $quantities = array_filter($request->input('psc', []), fn ($q) => (int) $q > 0);
        if (empty($quantities)) {
            return redirect()->back()->with('error', 'Please choose how many PSC slots you want to purchase.');
        }

        try {
            app(CartService::class)->addPscTopUpToCart($user, $quantities);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('companies.psc.topup.checkout')
            ->with('success', 'PSC top-up added. Review the details below and pay to increase your allowance.');
    }

    /**
     * Dedicated, formless checkout for a PSC top-up. The charge is collected by
     * Stripe as a pro-rated subscription invoice against the user's existing
     * subscription, so there is no billing form or card entry.
     */
    public function topUpCheckout()
    {
        $user = auth()->user();
        if (!$user || !$user->subscribed('default')) {
            return redirect()->route('companies.index')->with('error', 'You need an active Virtual Address plan to purchase PSC slots.');
        }
        $plan = $user->subscription('default')->plan;
        if (!$plan || !$plan->allowsCompanyPsc()) {
            return redirect()->route('companies.index')->with('error', 'Your current plan does not include Company Person with Significant Control.');
        }

        Cart::instance('psc_topup');
        $cartItems = Cart::content();
        if ($cartItems->isEmpty()) {
            Cart::instance('default');
            return redirect()->route('companies.index')->with('error', 'Your PSC top-up is empty. Please choose how many PSC slots you want to purchase.');
        }

        $billing = UserBilling::where('user_id', $user->id)->latest()->first();

        return view('front.checkout.psc-topup', [
            'cartItems' => $cartItems,
            'billing' => $billing,
            'interval' => app(PscService::class)->intervalForUser($user),
        ]);
    }

    /**
     * Apply the PSC top-up: increase the billed allowance (and the matching
     * Stripe subscription item quantity with proration), create a paid order
     * and clear only the PSC top-up cart instance.
     */
    public function topUpPay(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->subscribed('default')) {
            return redirect()->route('companies.index')->with('error', 'You need an active Virtual Address plan to purchase PSC slots.');
        }
        $plan = $user->subscription('default')->plan;
        if (!$plan || !$plan->allowsCompanyPsc()) {
            return redirect()->route('companies.index')->with('error', 'Your current plan does not include Company Person with Significant Control.');
        }

        Cart::instance('psc_topup');
        if (Cart::content()->isEmpty()) {
            Cart::instance('default');
            return redirect()->route('companies.index')->with('error', 'Your PSC top-up is empty. Please choose how many PSC slots you want to purchase.');
        }

        $orderNo = generateOrderNumber();
        foreach (Cart::content() as $item) {
            app(CartService::class)->updateCartItem($item, [
                'order_no' => $orderNo,
                'payment_method' => PaymentMethodEnum::Stripe->value,
            ]);
        }

        try {
            app(PscService::class)->applyTopUpFromCart($user);

            $order = app(OrderService::class)->createOrder([]);
            $order->update(['status' => PaymentStatusEnum::Paid->value]);

            Cart::destroy();
            Cart::instance('default');

            return redirect()->route('checkout.success', $order->order_no)
                ->with('success', 'Payment successful! Your PSC allowance has been increased.');
        } catch (\Exception $e) {
            Cart::instance('default');
            return redirect()->route('companies.psc.topup.checkout')
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Discard the pending PSC top-up and return to the Companies page.
     */
    public function topUpCancel()
    {
        Cart::instance('psc_topup');
        Cart::destroy();
        Cart::instance('default');

        return redirect()->route('companies.index')->with('info', 'PSC top-up cancelled.');
    }
}