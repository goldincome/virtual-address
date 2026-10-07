<?php
namespace App\Services;

use App\Models\Company;
use App\Models\CompanyPsc;
use App\Models\PscSubscriptionItem;
use App\Models\PscType;
use App\Models\User;
use Stripe\StripeClient;
use Stripe\Checkout\Session as CheckoutSession;

class PscService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
    }

    /**
     * Persist the PSC subscription items created by a Stripe Checkout session.
     * Must be called before the cart is destroyed after a successful payment.
     */
    public function registerSubscriptionItemsFromCheckout(CheckoutSession $session): void
    {
        if (!$session->subscription) {
            return;
        }

        $user = null;
        if (!empty($session->client_reference_id)) {
            $user = User::find((int) $session->client_reference_id);
        }
        if (!$user && !empty($session->metadata->user_id)) {
            $user = User::find((int) $session->metadata->user_id);
        }
        if (!$user) {
            $user = auth()->user();
        }
        if (!$user) {
            return;
        }

        $pscCartItems = app(CartService::class)->getPscItemsFromCart();
        if ($pscCartItems->isEmpty()) {
            return;
        }

        try {
            $subscription = $this->stripe->subscriptions->retrieve($session->subscription, [
                'expand' => ['items'],
            ]);
        } catch (\Exception $e) {
            report($e);
            return;
        }

        foreach ($pscCartItems as $cartItem) {
            // Top-up items are applied to the existing subscription by
            // applyTopUpFromCart() and never belong to a checkout subscription.
            if (!empty($cartItem->options['top_up'])) {
                continue;
            }

            $pscType = PscType::find($cartItem->options->psc_type_id);
            if (!$pscType) {
                continue;
            }

            $stripeItemId = null;
            foreach ($subscription->items->data as $stripeItem) {
                if ($stripeItem->price->id === $cartItem->options->stripe_price_id) {
                    $stripeItemId = $stripeItem->id;
                    break;
                }
            }

            if (!$stripeItemId) {
                report(new \Exception(
                    'PSC subscription item not matched for psc_type ' . $pscType->id . ' in session ' . $session->id
                ));
            }

            $interval = $cartItem->options->subscription_type === 'yearly' ? 'year' : 'month';

            PscSubscriptionItem::updateOrCreate(
                ['user_id' => $user->id, 'psc_type_id' => $pscType->id],
                [
                    'stripe_subscription_item_id' => $stripeItemId,
                    'quantity' => (int) $cartItem->qty,
                    'interval' => $interval,
                ]
            );
        }
    }

    /**
     * Add a person of significant control to a company. Adding is only allowed
     * within the quantity the user has already paid for; extra slots must be
     * purchased first through the cart/checkout/payment flow.
     */
    public function addPerson(Company $company, PscType $pscType, array $data): CompanyPsc
    {
        $this->assertAllowance($company->user, $pscType);

        return CompanyPsc::create([
            'company_id' => $company->id,
            'psc_type_id' => $pscType->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'status' => 'active',
        ]);
    }

    public function suspendPsc(CompanyPsc $psc): void
    {
        if ($psc->status !== 'active') {
            return;
        }
        $psc->update(['status' => 'suspended']);
        $this->releaseUnit($psc->company->user, $psc->pscType);
    }

    public function activatePsc(CompanyPsc $psc): void
    {
        if ($psc->status === 'active') {
            return;
        }
        $this->assertAllowance($psc->company->user, $psc->pscType);
        $psc->update(['status' => 'active']);
    }

    public function suspendCompany(Company $company): void
    {
        foreach ($company->companyPscs()->active()->get() as $psc) {
            $psc->update(['status' => 'suspended']);
            $this->releaseUnit($company->user, $psc->pscType);
        }
        $company->update(['status' => 'suspended']);
    }

    public function activateCompany(Company $company): void
    {
        foreach ($company->companyPscs()->where('status', 'suspended')->get() as $psc) {
            $this->assertAllowance($company->user, $psc->pscType);
            $psc->update(['status' => 'active']);
        }
        $company->update(['status' => 'active']);
    }

    public function billedQuantity(User $user, PscType $pscType): int
    {
        $item = PscSubscriptionItem::where('user_id', $user->id)
            ->where('psc_type_id', $pscType->id)
            ->first();

        return $item ? (int) $item->quantity : 0;
    }

    public function activePersonCount(User $user, PscType $pscType): int
    {
        return CompanyPsc::where('psc_type_id', $pscType->id)
            ->whereHas('company', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', 'active');
            })
            ->where('status', 'active')
            ->count();
    }

    /**
     * Guard: a user may only add/reactivate PSCs within the quantity they paid for.
     */
    protected function assertAllowance(User $user, PscType $pscType): void
    {
        $billed = $this->billedQuantity($user, $pscType);
        $active = $this->activePersonCount($user, $pscType);

        if ($active >= $billed) {
            throw new \Exception(
                $billed > 0
                    ? "You have used all {$billed} paid Person with Significant Control slot(s). Please purchase more PSC slots before adding another."
                    : 'You have not purchased any Person with Significant Control slots yet. Please purchase PSC slots before adding one.'
            );
        }
    }

    /**
     * Apply the PSC top-up quantities bought through the cart/checkout/payment
     * flow. Each cart PSC item increases the paid allowance (and the matching
     * Stripe subscription item quantity) with proration, so Stripe collects the
     * pro-rated amount through the subscription billing cycle.
     */
    public function applyTopUpFromCart(User $user): void
    {
        $cartService = app(CartService::class);

        // A top-up cart contains PSC items but no virtual-address plan.
        if ($cartService->checkIfCartHasVirtualAddress()) {
            return;
        }

        foreach ($cartService->getPscItemsFromCart() as $cartItem) {
            $pscType = PscType::find($cartItem->options->psc_type_id ?? null);
            if (!$pscType) {
                continue;
            }
            $this->increaseBilledQuantity($user, $pscType, (int) $cartItem->qty);
        }
    }

    /**
     * Increase the paid/billed allowance for a PSC type. The PSC is a
     * subscription item, so the quantity change is made with proration:
     * Stripe generates and collects an invoice for the pro-rated amount of
     * the current period and the full new quantity bills at the next renewal.
     */
    public function increaseBilledQuantity(User $user, PscType $pscType, int $quantity): void
    {
        if ($quantity < 1) {
            return;
        }

        $subscription = $user->subscription('default');
        if (!$subscription || !$subscription->active()) {
            throw new \Exception('An active subscription is required to purchase Person with Significant Control slots.');
        }

        if (!$pscType->status) {
            throw new \Exception('This Person with Significant Control type is currently unavailable.');
        }

        $item = PscSubscriptionItem::firstOrNew([
            'user_id' => $user->id,
            'psc_type_id' => $pscType->id,
        ]);

        $interval = $item->interval ?: $this->intervalForUser($user);

        if ($item->stripe_subscription_item_id) {
            $newQuantity = (int) $item->quantity + $quantity;
            $this->stripe->subscriptionItems->update($item->stripe_subscription_item_id, [
                'quantity' => $newQuantity,
                'proration_behavior' => 'create_prorations',
            ]);
            $item->quantity = $newQuantity;
        } else {
            $priceId = $pscType->stripePriceIdForInterval($interval);
            if (!$priceId) {
                throw new \Exception('Stripe pricing is not configured for this Person with Significant Control type.');
            }
            $stripeItem = $this->stripe->subscriptionItems->create([
                'subscription' => $subscription->stripe_id,
                'price' => $priceId,
                'quantity' => $quantity,
            ]);
            $item->stripe_subscription_item_id = $stripeItem->id;
            $item->quantity = $quantity;
            $item->interval = $interval;
        }

        $item->save();
    }

    /**
     * Stop billing for one unit (used when suspending a PSC or company).
     */
    protected function releaseUnit(User $user, PscType $pscType): void
    {
        $item = PscSubscriptionItem::where('user_id', $user->id)
            ->where('psc_type_id', $pscType->id)
            ->first();

        if (!$item) {
            return;
        }

        $newQuantity = max(0, (int) $item->quantity - 1);

        try {
            if ($item->stripe_subscription_item_id) {
                if ($newQuantity <= 0) {
                    $this->stripe->subscriptionItems->delete($item->stripe_subscription_item_id, [
                        'proration_behavior' => 'create_prorations',
                    ]);
                } else {
                    $this->stripe->subscriptionItems->update($item->stripe_subscription_item_id, [
                        'quantity' => $newQuantity,
                        'proration_behavior' => 'create_prorations',
                    ]);
                }
            }
        } catch (\Exception $e) {
            report($e);
            throw new \Exception('Failed to cancel the Stripe billing for this person: ' . $e->getMessage());
        }

        if ($newQuantity <= 0) {
            $item->delete();
        } else {
            $item->quantity = $newQuantity;
            $item->save();
        }
    }

    /**
     * Determine the billing interval of the user's current subscription.
     */
    public function intervalForUser(User $user): string
    {
        $subscription = $user->subscription('default');
        if (!$subscription) {
            return 'month';
        }

        try {
            $plan = $subscription->plan;
            $stripePriceId = $subscription->items->first()->stripe_price ?? null;
            if ($plan && $stripePriceId && $stripePriceId === $plan->stripe_price_id_yearly) {
                return 'year';
            }
        } catch (\Exception $e) {
            report($e);
        }

        return 'month';
    }
}
