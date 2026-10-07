<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Product;
use App\Models\Feature;
use App\Models\PscType;
use App\Models\User;
use App\Models\MailSetting;
use App\Models\FeatureSetting;
use App\Enums\ProductTypeEnum;
use App\Enums\MailTypeEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Gloudemans\Shoppingcart\Facades\Cart;
use App\Actions\CalculateCartTotalDiscount;
use Database\Seeders\PremiumPackageSeeder;
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

    public function test_premium_card_lists_all_features_in_package_two_instead_of_repeating_them(): void
    {
        $product = Product::create([
            'name' => 'Virtual Address',
            'type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
            'intro' => 'A professional London business address',
            'price' => 0,
            'is_active' => true,
        ]);

        $sharedOne = FeatureSetting::withoutEvents(fn () => FeatureSetting::create([
            'name' => 'Shared Feature One',
            'slug' => 'shared-feature-one',
            'icon' => 'fa-check',
            'description' => '',
            'status' => true,
        ]));
        $sharedTwo = FeatureSetting::withoutEvents(fn () => FeatureSetting::create([
            'name' => 'Shared Feature Two',
            'slug' => 'shared-feature-two',
            'icon' => 'fa-check',
            'description' => '',
            'status' => true,
        ]));
        $premiumOnly = FeatureSetting::withoutEvents(fn () => FeatureSetting::create([
            'name' => 'Premium Exclusive Feature',
            'slug' => 'premium-exclusive-feature',
            'icon' => 'fa-check',
            'description' => '',
            'status' => true,
        ]));

        $packageTwo = Plan::withoutEvents(fn () => $product->plans()->create([
            'name' => 'Package Two',
            'slug' => 'package-two',
            'description' => 'Second tier',
            'is_active' => true,
            'price' => '25.00',
            'yearly_monthly_price' => '250.00',
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

        $premium = Plan::withoutEvents(fn () => $product->plans()->create([
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
            'level' => 2,
        ]));

        foreach ([[$packageTwo, [$sharedOne, $sharedTwo]], [$premium, [$sharedOne, $sharedTwo, $premiumOnly]]] as [$plan, $settings]) {
            foreach ($settings as $index => $setting) {
                Feature::withoutEvents(fn () => Feature::create([
                    'plan_id' => $plan->id,
                    'product_id' => $product->id,
                    'feature_setting_id' => $setting->id,
                    'description' => '',
                    'sort_order' => $index + 1,
                    'is_activated' => true,
                ]));
            }
        }

        $response = $this->get(route('virtual-address.index'));

        $response->assertOk()
            ->assertSee('All Features in Package Two')
            ->assertSee('Premium Exclusive Feature');

        $html = $response->getContent();

        // Shared features stay on the Package Two card (appears in the Packages
        // and Our Plans sections) but are no longer repeated on the Premium card.
        $this->assertSame(2, substr_count($html, 'Shared Feature One'));
        $this->assertSame(2, substr_count($html, 'Shared Feature Two'));
    }

    public function test_premium_attach_features_skips_removed_feature_slugs(): void
    {
        $this->assertSame([
            'high-speed-wi-fi',
            'capacity-up-to-8-people',
            'comfortable-seating',
            'power-outlets-accessible',
            'whiteboard-markers',
            'air-conditioned',
            'meeting-conference-room-access',
            'inclusive-mail-forwarding',
        ], PremiumPackageSeeder::REMOVED_FEATURE_SLUGS);

        $product = Product::create([
            'name' => 'Virtual Address',
            'type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
            'intro' => 'A professional London business address',
            'price' => 0,
            'is_active' => true,
        ]);

        $packageTwo = Plan::withoutEvents(fn () => $product->plans()->create([
            'name' => 'Package Two',
            'slug' => 'package-two',
            'description' => 'Second tier',
            'is_active' => true,
            'price' => '25.00',
            'yearly_monthly_price' => '250.00',
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

        $premium = Plan::withoutEvents(fn () => $product->plans()->create([
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
            'level' => 2,
        ]));

        $removedOne = FeatureSetting::withoutEvents(fn () => FeatureSetting::create([
            'name' => 'High-Speed Wi-Fi',
            'slug' => 'high-speed-wi-fi',
            'icon' => 'fa-wifi',
            'description' => '',
            'status' => true,
        ]));
        $removedTwo = FeatureSetting::withoutEvents(fn () => FeatureSetting::create([
            'name' => 'Air Conditioned',
            'slug' => 'air-conditioned',
            'icon' => 'fa-air',
            'description' => '',
            'status' => true,
        ]));
        $kept = FeatureSetting::withoutEvents(fn () => FeatureSetting::create([
            'name' => 'Business Address',
            'slug' => 'business-address',
            'icon' => 'fa-building',
            'description' => '',
            'status' => true,
        ]));

        // Package Two holds the two removed slugs plus one kept slug, mirroring how
        // the Premium Package inherits the Package Two feature set by cloning.
        foreach ([$removedOne, $removedTwo, $kept] as $index => $setting) {
            Feature::withoutEvents(fn () => Feature::create([
                'plan_id' => $packageTwo->id,
                'product_id' => $product->id,
                'feature_setting_id' => $setting->id,
                'description' => '',
                'sort_order' => $index + 1,
                'is_activated' => true,
            ]));
        }

        (new \ReflectionMethod(PremiumPackageSeeder::class, 'attachFeatures'))
            ->invoke(new PremiumPackageSeeder(), $premium, $product);

        $slugs = Feature::where('plan_id', $premium->id)
            ->with('featureSetting')
            ->get()
            ->pluck('featureSetting.slug');

        $this->assertContains('business-address', $slugs);
        $this->assertNotContains('high-speed-wi-fi', $slugs);
        $this->assertNotContains('air-conditioned', $slugs);
        $this->assertNotContains('meeting-conference-room-access', $slugs);
        $this->assertNotContains('inclusive-mail-forwarding', $slugs);
    }
}