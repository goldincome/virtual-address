<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use App\Models\Company;
use App\Models\PscType;
use App\Models\Product;
use App\Models\CompanyType;
use App\Models\MailSetting;
use App\Models\CompanyPsc;
use App\Enums\MailTypeEnum;
use App\Enums\ProductTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\UserTypeEnum;
use App\Services\PscService;
use App\Services\CartService;
use App\Services\CompanyService;
use App\Models\CompanySubscriptionItem;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\SubscriptionItem;
use Stripe\ApiRequestor;
use Tests\Support\FakeStripeHttpClient;
use Tests\TestCase;

class PremiumAllowanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cart::destroy();
    }

    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(null);
        Cart::destroy();
        parent::tearDown();
    }

    protected function fakeStripe(callable $responder): FakeStripeHttpClient
    {
        $client = new FakeStripeHttpClient($responder);
        ApiRequestor::setHttpClient($client);

        return $client;
    }

    private function createPremiumPlan(int $includedCompanies = 1, int $includedPscs = 1): Plan
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
            'included_companies' => $includedCompanies,
            'included_pscs' => $includedPscs,
            'allows_company_psc' => true,
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

    private function createCompanyType(): CompanyType
    {
        return CompanyType::withoutEvents(fn () => CompanyType::create([
            'name' => 'Additional Company',
            'description' => 'An additional registered company.',
            'price_monthly' => '9.99',
            'price_yearly' => '59.00',
            'status' => true,
            'stripe_price_id_monthly' => 'price_company_monthly',
            'stripe_price_id_yearly' => 'price_company_yearly',
        ]));
    }

    private function createPscType(): PscType
    {
        return PscType::withoutEvents(fn () => PscType::create([
            'name' => 'Individual PSC',
            'description' => 'A single person with significant control.',
            'price_monthly' => '4.99',
            'price_yearly' => '39.99',
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

    public function test_included_company_allowance_allows_first_company_without_purchase(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan(includedCompanies: 1, includedPscs: 1);
        $this->subscribeUserToPlan($user, $plan);

        $service = app(CompanyService::class);
        $this->assertSame(1, $service->includedCompanies($user));
        $this->assertTrue($service->canAddCompany($user));

        $service->assertCanAddCompany($user);

        $user->companies()->create(['company_name' => 'ABC Ltd', 'status' => 'active']);

        $this->assertSame(1, $service->activeCompanyCount($user));
        $this->assertFalse($service->canAddCompany($user));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('used all of your available company slots');
        $service->assertCanAddCompany($user);
    }

    public function test_included_psc_allowance_allows_first_person_without_paid_item(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan(includedCompanies: 1, includedPscs: 1);
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        $company = $user->companies()->create(['company_name' => 'ABC Ltd', 'status' => 'active']);

        $psc = app(PscService::class)->addPerson($company, $pscType, [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'phone' => null,
            'email' => null,
        ]);

        $this->assertSame('active', $psc->status);
        $this->assertSame(0, app(PscService::class)->billedQuantity($user, $pscType));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('used all of your available');

        app(PscService::class)->addPerson($company, $pscType, [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => null,
            'email' => null,
        ]);
    }

    public function test_company_topup_adds_cart_items_on_its_own_instance(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $companyType = $this->createCompanyType();

        app(CartService::class)->addCompanyTopUpToCart($user, [$companyType->id => 2]);

        Cart::instance('company_topup');
        $item = Cart::content()->first(fn ($i) => $i->options->type === ProductTypeEnum::COMPANY->value);

        $this->assertNotNull($item);
        $this->assertEquals('company_topup_' . $companyType->id, $item->id);
        $this->assertEquals(2, $item->qty);
        $this->assertEquals(9.99, (float) $item->price);
        $this->assertTrue((bool) $item->options['top_up']);

        Cart::instance('default');
        $this->assertTrue(Cart::content()->isEmpty());
    }

    public function test_company_topup_route_rejects_non_psc_plan(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Basic',
            'type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
            'intro' => 'Basic',
            'price' => 0,
            'is_active' => true,
        ]);
        $plan = Plan::withoutEvents(fn () => $product->plans()->create([
            'name' => 'Basic Plan',
            'slug' => 'basic',
            'description' => 'Basic',
            'is_active' => true,
            'price' => '10.00',
            'yearly_monthly_price' => '100.00',
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
        $this->subscribeUserToPlan($user, $plan, 'price_basic_monthly');
        $companyType = $this->createCompanyType();

        $response = $this->actingAs($user)->post(route('companies.company.topup'), [
            'company' => [$companyType->id => 1],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_company_topup_pay_increases_billed_quantity_and_creates_paid_order(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $companyType = $this->createCompanyType();

        $client = $this->fakeStripe(fn () => FakeStripeHttpClient::json([
            'id' => 'si_company_test',
            'object' => 'subscription_item',
            'quantity' => 2,
        ]));

        $this->actingAs($user)
            ->post(route('companies.company.topup'), [
                'company' => [$companyType->id => 2],
            ])
            ->assertRedirect(route('companies.company.topup.checkout'));

        $this->get(route('companies.company.topup.checkout'))->assertOk();

        $this->post(route('companies.company.topup.pay'))
            ->assertRedirect();

        $item = CompanySubscriptionItem::where('user_id', $user->id)
            ->where('company_type_id', $companyType->id)
            ->first();

        $this->assertNotNull($item);
        $this->assertEquals(2, $item->quantity);
        $this->assertSame('si_company_test', $item->stripe_subscription_item_id);

        $createRequests = $client->requestsFor('post', '/v1/subscription_items');
        $this->assertCount(1, $createRequests);
        $this->assertEquals('price_company_monthly', $createRequests[0]['params']['price']);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => PaymentStatusEnum::Paid->value,
        ]);

        Cart::instance('company_topup');
        $this->assertTrue(Cart::content()->isEmpty());
        Cart::instance('default');
    }

    public function test_company_topup_pay_updates_existing_stripe_item_quantity(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $companyType = $this->createCompanyType();

        CompanySubscriptionItem::create([
            'user_id' => $user->id,
            'company_type_id' => $companyType->id,
            'stripe_subscription_item_id' => 'si_existing',
            'quantity' => 1,
            'interval' => 'month',
        ]);

        $client = $this->fakeStripe(fn () => FakeStripeHttpClient::json([
            'id' => 'si_existing',
            'object' => 'subscription_item',
            'quantity' => 3,
        ]));

        $this->actingAs($user)
            ->post(route('companies.company.topup'), [
                'company' => [$companyType->id => 2],
            ])
            ->assertRedirect(route('companies.company.topup.checkout'));

        $this->post(route('companies.company.topup.pay'))->assertRedirect();

        $item = CompanySubscriptionItem::where('user_id', $user->id)
            ->where('company_type_id', $companyType->id)
            ->first();

        $this->assertEquals(3, $item->quantity);

        $updateRequests = $client->requestsFor('post', '/v1/subscription_items/si_existing');
        $this->assertCount(1, $updateRequests);
        $this->assertEquals(3, (int) $updateRequests[0]['params']['quantity']);
        $this->assertEquals('create_prorations', $updateRequests[0]['params']['proration_behavior']);
    }

    public function test_company_allowance_data_reports_included_and_remaining(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan(includedCompanies: 1, includedPscs: 1);
        $this->subscribeUserToPlan($user, $plan);
        $this->createCompanyType();

        $response = $this->actingAs($user)->get(route('companies.index'));
        $response->assertOk();

        $allowance = $response->viewData('companyAllowance');
        $this->assertIsArray($allowance);
        $this->assertCount(1, $allowance);
        $this->assertSame(1, $allowance[0]['included']);
        $this->assertSame(0, $allowance[0]['billed']);
        $this->assertSame(0, $allowance[0]['active']);
        $this->assertSame(1, $allowance[0]['remaining']);
        $this->assertEquals(9.99, $allowance[0]['price']);
    }

    public function test_company_topup_cancel_clears_the_topup_cart_only(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $companyType = $this->createCompanyType();

        $this->actingAs($user)->post(route('companies.company.topup'), [
            'company' => [$companyType->id => 1],
        ]);

        $this->post(route('companies.company.topup.cancel'))
            ->assertRedirect(route('companies.index'));

        Cart::instance('company_topup');
        $this->assertTrue(Cart::content()->isEmpty());
        Cart::instance('default');
    }

    public function test_admin_can_create_company_type(): void
    {
        Queue::fake();

        $admin = User::factory()->create(['user_type' => UserTypeEnum::SUPER_ADMIN->value]);

        $this->actingAs($admin)
            ->post(route('admin.company-types.store'), [
                'name' => 'Additional Company',
                'description' => 'Extra company slot',
                'price_monthly' => '9.99',
                'price_yearly' => '59.00',
                'status' => '1',
            ])
            ->assertRedirect(route('admin.company-types.index'));

        $this->assertDatabaseHas('company_types', [
            'name' => 'Additional Company',
            'price_monthly' => '9.99',
            'price_yearly' => '59.00',
            'status' => true,
        ]);
    }

    public function test_admin_can_update_company_type_price(): void
    {
        Queue::fake();

        $admin = User::factory()->create(['user_type' => UserTypeEnum::SUPER_ADMIN->value]);
        $companyType = $this->createCompanyType();

        $this->actingAs($admin)
            ->put(route('admin.company-types.update', $companyType), [
                'name' => 'Additional Company',
                'description' => 'Updated',
                'price_monthly' => '12.50',
                'price_yearly' => '120.00',
                'status' => '1',
            ])
            ->assertRedirect(route('admin.company-types.index'));

        $companyType->refresh();
        $this->assertSame('12.50', $companyType->price_monthly);
        $this->assertSame('120.00', $companyType->price_yearly);
    }

    public function test_admin_cannot_delete_company_type_with_subscribers(): void
    {
        Queue::fake();

        $admin = User::factory()->create(['user_type' => UserTypeEnum::SUPER_ADMIN->value]);
        $user = User::factory()->create();
        $companyType = $this->createCompanyType();

        CompanySubscriptionItem::create([
            'user_id' => $user->id,
            'company_type_id' => $companyType->id,
            'stripe_subscription_item_id' => 'si_keep',
            'quantity' => 1,
            'interval' => 'month',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.company-types.destroy', $companyType))
            ->assertRedirect(route('admin.company-types.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('company_types', ['id' => $companyType->id]);
    }

    public function test_admin_can_delete_company_type_without_subscribers(): void
    {
        Queue::fake();

        $admin = User::factory()->create(['user_type' => UserTypeEnum::SUPER_ADMIN->value]);
        $companyType = $this->createCompanyType();

        $this->actingAs($admin)
            ->delete(route('admin.company-types.destroy', $companyType))
            ->assertRedirect(route('admin.company-types.index'));

        $this->assertDatabaseMissing('company_types', ['id' => $companyType->id]);
    }
}
