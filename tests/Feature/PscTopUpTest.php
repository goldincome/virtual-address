<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use App\Models\Company;
use App\Models\PscType;
use App\Models\Product;
use App\Models\MailSetting;
use App\Models\CompanyPsc;
use App\Services\CartService;
use App\Services\PscService;
use App\Models\PscSubscriptionItem;
use App\Enums\ProductTypeEnum;
use App\Enums\MailTypeEnum;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Cashier\SubscriptionItem;
use Tests\TestCase;

class PscTopUpTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cart::destroy();
    }

    private function createPremiumPlan(): Plan
    {
        $product = Product::create([
            'name' => 'Virtual Address',
            'type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
            'intro' => 'A professional London business address',
            'price' => 0,
            'is_active' => true,
        ]);

        $plan = Plan::withoutEvents(fn () => $product->plans()->create([
            'name' => 'Premium Package',
            'slug' => 'premium-package',
            'description' => 'Test premium plan',
            'is_active' => true,
            'price' => '35.00',
            'yearly_monthly_price' => '350.00',
            'signup_fee' => '0.00',
            'currency' => 'gbp',
            'trial_period' => 0,
            'trial_interval' => 'month',
            'invoice_period' => 1,
            'invoice_interval' => 'month',
            'grace_period' => 0,
            'grace_interval' => 'day',
            'level' => 1,
        ]));

        MailSetting::withoutEvents(fn () => MailSetting::create([
            'plan_id' => $plan->id,
            'name' => 'Mail Scanning - Premium',
            'mail_type' => MailTypeEnum::Scanned->value,
            'price' => '1.00',
            'status' => true,
            'interval' => 'month',
            'stripe_price_name' => 'premium-package-mail-scanning',
        ]));

        return $plan;
    }

    private function createPscType(): PscType
    {
        return PscType::withoutEvents(fn () => PscType::create([
            'name' => 'Individual PSC',
            'description' => 'A single person with significant control.',
            'price_monthly' => '5.00',
            'price_yearly' => '50.00',
            'status' => true,
        ]));
    }

    private function subscribeUserToPlan(User $user, Plan $plan, string $priceId = 'price_premium_monthly'): void
    {
        Plan::withoutEvents(fn () => $plan->update([
            'stripe_price_id_monthly' => $priceId,
            'stripe_price_id_yearly' => null,
        ]));

        $subscription = $user->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => 'sub_test_' . Str::random(12),
            'stripe_status' => 'active',
            'stripe_price' => $priceId,
            'quantity' => 1,
        ]);

        SubscriptionItem::withoutEvents(fn () => SubscriptionItem::create([
            'subscription_id' => $subscription->id,
            'stripe_id' => 'si_test_' . Str::random(12),
            'stripe_product' => 'prod_test',
            'stripe_price' => $priceId,
            'quantity' => 1,
        ]));
    }

    public function test_psc_topup_adds_cart_items_without_a_plan(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        $this->actingAs($user);
        app(CartService::class)->addPscTopUpToCart($user, [$pscType->id => 2]);

        $pscItem = Cart::content()->first(fn ($item) => $item->options->type === ProductTypeEnum::PSC->value);

        $this->assertNotNull($pscItem);
        $this->assertEquals('psc_topup_' . $pscType->id, $pscItem->id);
        $this->assertEquals(2, $pscItem->qty);
        $this->assertEquals(5.0, (float) $pscItem->price);
        $this->assertTrue((bool) $pscItem->options->top_up);
        $this->assertNull($pscItem->options->stripe_price_id);
        $this->assertFalse(app(CartService::class)->checkIfCartHasVirtualAddress());
    }

    public function test_add_person_is_blocked_when_allowance_is_used_up(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        $company = $user->companies()->create(['company_name' => 'ABC Ltd', 'status' => 'active']);

        PscSubscriptionItem::create([
            'user_id' => $user->id,
            'psc_type_id' => $pscType->id,
            'stripe_subscription_item_id' => 'si_psc_test',
            'quantity' => 1,
            'interval' => 'month',
        ]);

        CompanyPsc::create([
            'company_id' => $company->id,
            'psc_type_id' => $pscType->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'status' => 'active',
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('used all 1 paid');

        app(PscService::class)->addPerson($company, $pscType, [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => null,
            'email' => null,
        ]);
    }

    public function test_add_person_is_blocked_without_paid_allowance(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        $company = $user->companies()->create(['company_name' => 'ABC Ltd', 'status' => 'active']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not purchased any');

        app(PscService::class)->addPerson($company, $pscType, [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => null,
            'email' => null,
        ]);
    }

    public function test_psc_topup_route_adds_allowance_items_to_cart(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        $this->actingAs($user)
            ->post(route('companies.psc.topup'), ['psc' => [$pscType->id => '3']])
            ->assertRedirect(route('cart.index'));

        $pscItem = Cart::content()->first(fn ($item) => $item->options->type === ProductTypeEnum::PSC->value);

        $this->assertNotNull($pscItem);
        $this->assertEquals(3, $pscItem->qty);
        $this->assertEquals(5.0, (float) $pscItem->price);
    }

    public function test_sidebar_shows_company_psc_link_only_for_psc_plans(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);

        // With a PSC-capable plan the sidebar link must be present.
        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Company / PSC');

        // A user without such a plan must not see the link.
        $plainUser = User::factory()->create();
        $this->actingAs($plainUser)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Company / PSC');
    }
}