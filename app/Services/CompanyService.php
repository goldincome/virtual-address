<?php
namespace App\Services;

use App\Models\Company;
use App\Models\CompanySubscriptionItem;
use App\Models\CompanyType;
use App\Models\User;
use Stripe\StripeClient;
use Stripe\Checkout\Session as CheckoutSession;

class CompanyService
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('cashier.secret'));
    }

    /**
     * Persist the company subscription items created by a Stripe Checkout
     * session. Mirrors PscService::registerSubscriptionItemsFromCheckout().
     */
    public function registerSubscriptionItemsFromCheckout(CheckoutSession $session): void
    {
        if (! $session->subscription) {
            return;
        }

        $user = null;
        if (! empty($session->client_reference_id)) {
            $user = User::find((int) $session->client_reference_id);
        }
        if (! $user && ! empty($session->metadata->user_id)) {
            $user = User::find((int) $session->metadata->user_id);
        }
        if (! $user) {
            $user = auth()->user();
        }
        if (! $user) {
            return;
        }

        $companyCartItems = app(CartService::class)->getCompanyItemsFromCart();
        if ($companyCartItems->isEmpty()) {
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

        foreach ($companyCartItems as $cartItem) {
            if (! empty($cartItem->options['top_up'])) {
                continue;
            }

            $companyType = CompanyType::find($cartItem->options->company_type_id);
            if (! $companyType) {
                continue;
            }

            $stripeItemId = null;
            foreach ($subscription->items->data as $stripeItem) {
                if ($stripeItem->price->id === $cartItem->options->stripe_price_id) {
                    $stripeItemId = $stripeItem->id;
                    break;
                }
            }

            if (! $stripeItemId) {
                report(new \Exception(
                    'Company subscription item not matched for company_type ' . $companyType->id . ' in session ' . $session->id
                ));
            }

            $interval = ($cartItem->options->subscription_type ?? 'monthly') === 'yearly' ? 'year' : 'month';

            CompanySubscriptionItem::updateOrCreate(
                ['user_id' => $user->id, 'company_type_id' => $companyType->id],
                [
                    'stripe_subscription_item_id' => $stripeItemId,
                    'quantity' => (int) $cartItem->qty,
                    'interval' => $interval,
                ]
            );
        }
    }

    public function includedCompanies(User $user): int
    {
        $subscription = $user->subscription('default');

        return $subscription && $subscription->plan ? (int) $subscription->plan->included_companies : 0;
    }

    public function billedCompanyQuantity(User $user, ?CompanyType $type = null): int
    {
        $query = CompanySubscriptionItem::where('user_id', $user->id);

        if ($type) {
            $query->where('company_type_id', $type->id);
        }

        return (int) $query->sum('quantity');
    }

    public function activeCompanyCount(User $user): int
    {
        return Company::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();
    }

    public function canAddCompany(User $user): bool
    {
        $effective = $this->includedCompanies($user) + $this->billedCompanyQuantity($user);

        return $this->activeCompanyCount($user) < $effective;
    }

    public function assertCanAddCompany(User $user): void
    {
        if (! $this->canAddCompany($user)) {
            throw new \Exception(
                'You have used all of your available company slots. Please purchase an additional company before adding another.'
            );
        }
    }

    /**
     * Increase the paid/billed company allowance. Mirrors
     * PscService::increaseBilledQuantity(): the company is a subscription item,
     * so the quantity change is prorated against the current period.
     */
    public function increaseBilledCompanyQuantity(User $user, CompanyType $type, int $quantity): void
    {
        if ($quantity < 1) {
            return;
        }

        $subscription = $user->subscription('default');
        if (! $subscription || ! $subscription->active()) {
            throw new \Exception('An active subscription is required to purchase additional companies.');
        }

        if (! $type->status) {
            throw new \Exception('This company type is currently unavailable.');
        }

        $item = CompanySubscriptionItem::firstOrNew([
            'user_id' => $user->id,
            'company_type_id' => $type->id,
        ]);

        $interval = $item->interval ?: app(PscService::class)->intervalForUser($user);

        if ($item->stripe_subscription_item_id) {
            $newQuantity = (int) $item->quantity + $quantity;
            $this->stripe->subscriptionItems->update($item->stripe_subscription_item_id, [
                'quantity' => $newQuantity,
                'proration_behavior' => 'create_prorations',
            ]);
            $item->quantity = $newQuantity;
        } else {
            $priceId = $type->stripePriceIdForInterval($interval);
            if (! $priceId) {
                throw new \Exception('Stripe pricing is not configured for this company type.');
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
     * Apply the company top-up quantities bought through the cart/checkout
     * flow, increasing the paid allowance with proration.
     */
    public function applyCompanyTopUpFromCart(User $user): void
    {
        $cartService = app(CartService::class);

        if ($cartService->checkIfCartHasVirtualAddress()) {
            return;
        }

        foreach ($cartService->getCompanyItemsFromCart() as $cartItem) {
            $companyType = CompanyType::find($cartItem->options->company_type_id ?? null);
            if (! $companyType) {
                continue;
            }
            $this->increaseBilledCompanyQuantity($user, $companyType, (int) $cartItem->qty);
        }
    }
}
