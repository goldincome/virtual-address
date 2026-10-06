<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Product;
use App\Models\PscType;
use App\Models\User;
use App\Models\MailSetting;
use App\Enums\ProductTypeEnum;
use App\Enums\MailTypeEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Actions\CalculateCartTotalDiscount;
use Tests\TestCase;

class PscCartTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_plan_page_shows_psc_quantity_selector_when_plan_allows_it(): void
    {
        $plan = $this->createPremiumPlan();
        $this->createPscType();

        $this->get(route('virtual-address.show', $plan->slug))
            ->assertOk()
            ->assertSee('Company Person with Significant Control (PSC)')
            ->assertSee('Individual PSC')
            ->assertSee('name="psc[' . PscType::first()->id . ']"', false);
    }

    public function test_psc_quantity_is_added_to_cart_with_the_plan(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $pscType = $this->createPscType();

        $this->actingAs($user)
            ->post(route('virtual-address.store', ['plan_id' => $plan->id]), [
                'subscription_type' => 'monthly',
                'mail_type' => MailTypeEnum::Scanned->value,
                'psc' => [$pscType->id => '2'],
            ])
            ->assertRedirect(route('cart.index'));

        $items = Cart::content();

        $planItem = $items->first(fn ($item) => $item->options->type === ProductTypeEnum::VIRTUAL_ADDRESS->value);
        $this->assertNotNull($planItem);
        $this->assertEquals(1, $planItem->qty);
        $this->assertEquals(35.0, (float) $planItem->price);

        $pscItem = $items->first(fn ($item) => $item->options->type === ProductTypeEnum::PSC->value);
        $this->assertNotNull($pscItem);
        $this->assertEquals('Company PSC - Individual PSC', $pscItem->name);
        $this->assertEquals(2, $pscItem->qty);
        $this->assertEquals(5.0, (float) $pscItem->price);
        $this->assertEquals($pscType->id, $pscItem->options->psc_type_id);
        $this->assertEquals('psc_' . $pscType->id, $pscItem->id);

        // PSC subtotal (2 x £5) must be reflected in the cart
        $this->assertEquals(10.00, (float) $pscItem->subtotal());
    }

    public function test_psc_included_in_monthly_subscription_stripe_line_items(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $pscType = $this->createPscType();

        $this->actingAs($user)
            ->post(route('virtual-address.store', ['plan_id' => $plan->id]), [
                'subscription_type' => 'monthly',
                'mail_type' => MailTypeEnum::Scanned->value,
                'psc' => [$pscType->id => '3'],
            ]);

        $gateway = new \App\Services\PaymentGateways\StripeGateway();
        $cartService = app(\App\Services\CartService::class);

        // Mirror the line items StripeGateway merges for a subscription checkout
        $pscLineItems = [];
        foreach ($cartService->getPscItemsFromCart() as $pscItem) {
            $pscLineItems[] = [
                'price' => $pscItem->options->stripe_price_id,
                'quantity' => (int) $pscItem->qty,
            ];
        }

        $this->assertCount(1, $pscLineItems);
        $this->assertEquals(3, $pscLineItems[0]['quantity']);

        // One-time items (rooms) must NOT include PSC or the VA plan
        $oneTime = (new \ReflectionMethod($gateway, 'getOneTimeItemsFromCart'))->invoke($gateway);
        $names = collect($oneTime)->pluck('price_data.product_data.name')->all();
        $this->assertStringNotContainsString('Company PSC', implode(' ', $names));
        $this->assertStringNotContainsString($plan->name, implode(' ', $names));
    }

    public function test_calculate_cart_total_discount_handles_psc_items_without_error(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $pscType = $this->createPscType();

        $this->actingAs($user)
            ->post(route('virtual-address.store', ['plan_id' => $plan->id]), [
                'subscription_type' => 'monthly',
                'mail_type' => MailTypeEnum::Scanned->value,
                'psc' => [$pscType->id => '2'],
            ]);

        // Must not throw count() TypeError now that PSC items sit in the cart
        $this->assertSame(0.0, (float) app(CalculateCartTotalDiscount::class)->execute());
    }
}