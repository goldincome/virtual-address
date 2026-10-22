<?php
namespace App\Http\Controllers\Front;

use App\Models\CompanyType;
use App\Models\UserBilling;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\CompanyService;
use App\Services\PscService;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Gloudemans\Shoppingcart\Facades\Cart;

class CompanyTopUpController extends Controller
{
    /**
     * Add the requested additional-company quantities (top-up) to the cart so
     * the user can pay for more company slots through the checkout flow.
     */
    public function topUp(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->subscribed('default')) {
            return redirect()->back()->with('error', 'You need an active Virtual Address plan to purchase additional companies.');
        }
        $plan = $user->subscription('default')->plan;
        if (!$plan || !$plan->allowsCompanyPsc()) {
            return redirect()->back()->with('error', 'Your current plan does not include Company Person with Significant Control.');
        }

        $request->validate([
            'company' => 'required|array',
            'company.*' => 'nullable|integer|min:0|max:100',
        ]);

        $quantities = array_filter($request->input('company', []), fn ($q) => (int) $q > 0);
        if (empty($quantities)) {
            return redirect()->back()->with('error', 'Please choose how many additional companies you want to purchase.');
        }

        try {
            app(CartService::class)->addCompanyTopUpToCart($user, $quantities);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('companies.company.topup.checkout')
            ->with('success', 'Additional company added. Review the details below and pay to increase your allowance.');
    }

    /**
     * Dedicated, formless checkout for a company top-up. The charge is
     * collected by Stripe as a pro-rated subscription invoice against the
     * user's existing subscription, so there is no billing form or card entry.
     */
    public function topUpCheckout()
    {
        $user = auth()->user();
        if (!$user || !$user->subscribed('default')) {
            return redirect()->route('companies.index')->with('error', 'You need an active Virtual Address plan to purchase additional companies.');
        }
        $plan = $user->subscription('default')->plan;
        if (!$plan || !$plan->allowsCompanyPsc()) {
            return redirect()->route('companies.index')->with('error', 'Your current plan does not include Company Person with Significant Control.');
        }

        Cart::instance('company_topup');
        $cartItems = Cart::content();
        if ($cartItems->isEmpty()) {
            Cart::instance('default');
            return redirect()->route('companies.index')->with('error', 'Your company top-up is empty. Please choose how many additional companies you want to purchase.');
        }

        $billing = UserBilling::where('user_id', $user->id)->latest()->first();

        return view('front.checkout.company-topup', [
            'cartItems' => $cartItems,
            'billing' => $billing,
            'interval' => app(PscService::class)->intervalForUser($user),
        ]);
    }

    /**
     * Apply the company top-up: increase the billed allowance (and the matching
     * Stripe subscription item quantity with proration), create a paid order
     * and clear only the company top-up cart instance.
     */
    public function topUpPay(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->subscribed('default')) {
            return redirect()->route('companies.index')->with('error', 'You need an active Virtual Address plan to purchase additional companies.');
        }
        $plan = $user->subscription('default')->plan;
        if (!$plan || !$plan->allowsCompanyPsc()) {
            return redirect()->route('companies.index')->with('error', 'Your current plan does not include Company Person with Significant Control.');
        }

        Cart::instance('company_topup');
        if (Cart::content()->isEmpty()) {
            Cart::instance('default');
            return redirect()->route('companies.index')->with('error', 'Your company top-up is empty. Please choose how many additional companies you want to purchase.');
        }

        $orderNo = generateOrderNumber();
        foreach (Cart::content() as $item) {
            app(CartService::class)->updateCartItem($item, [
                'order_no' => $orderNo,
                'payment_method' => PaymentMethodEnum::Stripe->value,
            ]);
        }

        try {
            app(CompanyService::class)->applyCompanyTopUpFromCart($user);

            $order = app(OrderService::class)->createOrder([]);
            $order->update(['status' => PaymentStatusEnum::Paid->value]);

            Cart::destroy();
            Cart::instance('default');

            return redirect()->route('checkout.success', $order->order_no)
                ->with('success', 'Payment successful! Your company allowance has been increased.');
        } catch (\Exception $e) {
            Cart::instance('default');
            return redirect()->route('companies.company.topup.checkout')
                ->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Discard the pending company top-up and return to the Companies page.
     */
    public function topUpCancel()
    {
        Cart::instance('company_topup');
        Cart::destroy();
        Cart::instance('default');

        return redirect()->route('companies.index')->with('info', 'Company top-up cancelled.');
    }
}
