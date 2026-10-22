<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use App\Models\Company;
use App\Models\PscType;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\MailSetting;
use App\Models\CompanyPsc;
use App\Services\CartService;
use App\Services\PscService;
use App\Models\PscSubscriptionItem;
use App\Enums\ProductTypeEnum;
use App\Enums\MailTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\UserTypeEnum;
use App\Mail\PaymentReceiptEmail;
use App\Jobs\SendPaymentReceiptEmail;
use App\Http\Controllers\Front\PaymentWebhookController;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Queue;
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

        Cart::instance('psc_topup');
        $pscItem = Cart::content()->first(fn ($item) => $item->options->type === ProductTypeEnum::PSC->value);

        $this->assertNotNull($pscItem);
        $this->assertEquals('psc_topup_' . $pscType->id, $pscItem->id);
        $this->assertEquals(2, $pscItem->qty);
        $this->assertEquals(5.0, (float) $pscItem->price);
        $this->assertTrue((bool) $pscItem->options['top_up']);
        $this->assertNull($pscItem->options->stripe_price_id);

        // The top-up lives on its own cart instance; the main cart stays empty.
        Cart::instance('default');
        $this->assertTrue(Cart::content()->isEmpty());
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
        $this->expectExceptionMessage('used all of your available');

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
        $this->expectExceptionMessage('used all of your available');

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
            ->assertRedirect(route('companies.psc.topup.checkout'));

        Cart::instance('psc_topup');
        $pscItem = Cart::content()->first(fn ($item) => $item->options->type === ProductTypeEnum::PSC->value);

        $this->assertNotNull($pscItem);
        $this->assertEquals(3, $pscItem->qty);
        $this->assertEquals(5.0, (float) $pscItem->price);

        Cart::instance('default');
        $this->assertTrue(Cart::content()->isEmpty());
    }

    public function test_psc_topup_checkout_page_shows_items_and_is_formless(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        $this->actingAs($user);
        app(CartService::class)->addPscTopUpToCart($user, [$pscType->id => 2]);

        $this->get(route('companies.psc.topup.checkout'))
            ->assertOk()
            ->assertSee('Company PSC - ' . $pscType->name)
            ->assertSee('Pay Now')
            ->assertDontSee('name="billing_address"')
            ->assertDontSee('name="postal_code"');
    }

    public function test_psc_topup_checkout_page_empty_redirects_back(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);

        $this->actingAs($user)
            ->get(route('companies.psc.topup.checkout'))
            ->assertRedirect(route('companies.index'));
    }

    public function test_psc_topup_pay_creates_paid_order_and_clears_psc_instance(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        // The Stripe subscription item update is mocked so the test does not
        // make network calls; everything else in the payment path runs for real.
        $pscService = \Mockery::mock(\App\Services\PscService::class)->makePartial();
        $pscService->shouldReceive('increaseBilledQuantity')
            ->once()
            ->withArgs(fn ($user, $pscType, $quantity) => (int) $quantity === 2);
        $this->app->instance(\App\Services\PscService::class, $pscService);

        $this->actingAs($user);
        app(CartService::class)->addPscTopUpToCart($user, [$pscType->id => 2]);

        $response = $this->post(route('companies.psc.topup.pay'));

        $this->app->forgetInstance(\App\Services\PscService::class);

        $order = \App\Models\Order::first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('checkout.success', $order->order_no));

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'paid', 'payment_method' => 'stripe']);
        $this->assertTrue($order->orderDetails()->where('product_type', ProductTypeEnum::PSC->value)->exists());

        \Mockery::close();

        Cart::instance('psc_topup');
        $this->assertTrue(Cart::content()->isEmpty());

        Cart::instance('default');
        $this->assertTrue(Cart::content()->isEmpty());
    }

    public function test_psc_topup_pay_does_not_touch_the_main_cart(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        $pscService = \Mockery::mock(\App\Services\PscService::class)->makePartial();
        $pscService->shouldReceive('increaseBilledQuantity')->once();
        $this->app->instance(\App\Services\PscService::class, $pscService);

        $this->actingAs($user);
        app(CartService::class)->addPscTopUpToCart($user, [$pscType->id => 1]);

        // The main cart holds an unrelated room item that must survive the top-up.
        Cart::instance('default');
        Cart::add('room_1', 'Meeting Room - Standard', 1, 20.0, 0, [
            'type' => ProductTypeEnum::MEETING_ROOM->value,
        ]);

        $this->post(route('companies.psc.topup.pay'))->assertRedirect();

        $this->app->forgetInstance(\App\Services\PscService::class);

        Cart::instance('default');
        $this->assertTrue(Cart::content()->contains(fn ($item) => $item->id === 'room_1'));

        Cart::instance('psc_topup');
        $this->assertTrue(Cart::content()->isEmpty());
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

    public function test_psc_quantity_shows_billing_interval_unit_in_order_email(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        $order = $user->orders()->create([
            'order_no' => 'VA-TEST-EMA1',
            'status' => PaymentStatusEnum::Paid->value,
            'sub_total' => '10.00',
            'tax' => 0,
            'total' => '10.00',
            'currency' => 'gbp',
            'payment_method' => 'stripe',
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'ref_no' => 'VA-TEST-EMA1',
            'name' => 'Person with Significant Control - ' . $pscType->name,
            'product_type' => ProductTypeEnum::PSC->value,
            'product_id' => $plan->product_id,
            'quantity' => 2,
            'price' => '5.00',
            'sub_total' => '10.00',
        ]);

        $html = (new PaymentReceiptEmail($order->load('orderDetails', 'user')))->render();

        $this->assertStringContainsString('2 mon', $html);
        $this->assertStringNotContainsString('2hr(s)', $html);
    }

    public function test_order_email_shows_mail_delivery_type_and_psc_quantity(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        $planJson = json_encode([
            'id' => $plan->id,
            'name' => 'Premium Package',
            'slug' => 'premium-package',
            'price' => '35.00',
            'yearly_monthly_price' => '350.00',
            'subscription_type' => 'monthly',
            'mail_type' => MailTypeEnum::Scanned->value,
            'mail_settings' => [
                ['name' => 'Mail Scanning - Premium', 'mail_type' => MailTypeEnum::Scanned->value, 'price' => '1.00'],
            ],
        ]);

        $order = $user->orders()->create([
            'order_no' => 'VA-TEST-MAIL1',
            'status' => PaymentStatusEnum::Paid->value,
            'sub_total' => '45.00',
            'tax' => 0,
            'total' => '45.00',
            'currency' => 'gbp',
            'payment_method' => 'stripe',
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'ref_no' => 'VA-TEST-MAIL1-1',
            'name' => 'Virtual Address - Premium Package',
            'product_type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
            'product_id' => $plan->product_id,
            'quantity' => 1,
            'price' => '35.00',
            'sub_total' => '35.00',
            'plan' => $planJson,
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'ref_no' => 'VA-TEST-MAIL1-2',
            'name' => 'Company PSC - ' . $pscType->name,
            'product_type' => ProductTypeEnum::PSC->value,
            'product_id' => $plan->product_id,
            'quantity' => 2,
            'price' => '5.00',
            'sub_total' => '10.00',
        ]);

        $html = (new PaymentReceiptEmail($order->load('orderDetails', 'user')))->render();

        $this->assertStringContainsString('Mail Scanning (All your mails will be scanned to you)', $html);
        $this->assertStringContainsString('Slots purchased on this order:', $html);
        $this->assertStringContainsString('2 mon &times; Company PSC - ' . $pscType->name, $html);
    }

    public function test_payment_receipt_email_renders_for_new_subscriber_without_subscription(): void
    {
        // Regression: a brand new subscriber has no local subscription row until the
        // Stripe webhook is processed, yet the confirmation/receipt email must still
        // render (it previously threw "Attempt to read property created_at on null").
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();

        $this->assertNull($user->subscription('default'));

        $planJson = json_encode([
            'id' => $plan->id,
            'name' => 'Package Two',
            'slug' => 'package-twoo',
            'price' => '15.99',
            'yearly_monthly_price' => '99.00',
            'subscription_type' => 'monthly',
            'mail_type' => MailTypeEnum::Scanned->value,
            'mail_settings' => [
                ['name' => 'Mail Scanning - Premium', 'mail_type' => MailTypeEnum::Scanned->value, 'price' => '1.00'],
            ],
        ]);

        $order = $user->orders()->create([
            'order_no' => 'VA-TEST-NOSUB1',
            'status' => PaymentStatusEnum::Paid->value,
            'sub_total' => '20.99',
            'tax' => 0,
            'total' => '20.99',
            'currency' => 'gbp',
            'payment_method' => 'stripe',
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'ref_no' => 'VA-TEST-NOSUB1-1',
            'name' => 'Virtual Address - Package Two',
            'product_type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
            'product_id' => $plan->product_id,
            'quantity' => 1,
            'price' => '15.99',
            'sub_total' => '15.99',
            'plan' => $planJson,
        ]);

        $order->load('orderDetails', 'user');

        $html = (new PaymentReceiptEmail($order))->render();

        $this->assertStringContainsString('Subscription Details', $html);
        $this->assertStringContainsString('Activation Date', $html);
        $this->assertStringContainsString('Package Two', $html);

        // Both PDF attachments (invoice + service agreement) must build too.
        $this->assertCount(2, (new PaymentReceiptEmail($order))->attachments());
    }

    public function test_invoice_pdf_shows_mail_delivery_type_and_psc_quantity(): void
    {
        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        $planJson = json_encode([
            'id' => $plan->id,
            'name' => 'Premium Package',
            'slug' => 'premium-package',
            'price' => '35.00',
            'yearly_monthly_price' => '350.00',
            'subscription_type' => 'monthly',
            'mail_type' => MailTypeEnum::Scanned->value,
            'mail_settings' => [
                ['name' => 'Mail Scanning - Premium', 'mail_type' => MailTypeEnum::Scanned->value, 'price' => '1.00'],
            ],
        ]);

        $order = $user->orders()->create([
            'order_no' => 'VA-TEST-MAIL2',
            'status' => PaymentStatusEnum::Paid->value,
            'sub_total' => '45.00',
            'tax' => 0,
            'total' => '45.00',
            'currency' => 'gbp',
            'payment_method' => 'stripe',
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'ref_no' => 'VA-TEST-MAIL2-1',
            'name' => 'Virtual Address - Premium Package',
            'product_type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
            'product_id' => $plan->product_id,
            'quantity' => 1,
            'price' => '35.00',
            'sub_total' => '35.00',
            'plan' => $planJson,
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'ref_no' => 'VA-TEST-MAIL2-2',
            'name' => 'Company PSC - ' . $pscType->name,
            'product_type' => ProductTypeEnum::PSC->value,
            'product_id' => $plan->product_id,
            'quantity' => 2,
            'price' => '5.00',
            'sub_total' => '10.00',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('invoices.download', $order->order_no));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));

        $html = view('pdf.invoice', [
            'order' => $order->load('orderDetails', 'user'),
            'jsonPlan' => json_decode($planJson),
            'subscriptionType' => \App\Enums\SubscriptionTypeEnum::class,
        ])->render();

        $this->assertStringContainsString('Mail Scanning', $html);
        $this->assertStringContainsString('Company PSC - ' . $pscType->name, $html);
        $this->assertStringContainsString('2 mon', $html);
    }

    public function test_payment_receipt_email_is_sent_exactly_once_on_first_purchase(): void
    {
        Queue::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_test_webhook']);
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);

        $order = $user->orders()->create([
            'order_no' => 'VA-TEST-WEB1',
            'status' => PaymentStatusEnum::Paid->value,
            'sub_total' => '10.00',
            'tax' => 0,
            'total' => '10.00',
            'currency' => 'gbp',
            'payment_method' => 'stripe',
        ]);

        // Both webhooks fire for the same first-purchase order. Only the
        // invoice.paid handler may send the payment confirmation receipt.
        $subscriptionCreatedPayload = [
            'id' => 'evt_sub_created_1',
            'data' => [
                'object' => [
                    'id' => 'sub_web1',
                    'customer' => 'cus_test_webhook',
                    'status' => 'active',
                    'metadata' => ['order_no' => 'VA-TEST-WEB1', 'type' => 'default'],
                    'items' => [
                        'data' => [
                            [
                                'id' => 'si_web_item_1',
                                'price' => ['id' => 'price_premium_monthly', 'product' => 'prod_test'],
                                'quantity' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        app(PaymentWebhookController::class)->handleCustomerSubscriptionCreated($subscriptionCreatedPayload);

        Queue::assertNotPushed(SendPaymentReceiptEmail::class);

        $invoicePaidPayload = [
            'id' => 'evt_invoice_paid_1',
            'data' => [
                'object' => [
                    'customer' => 'cus_test_webhook',
                    'subscription' => 'sub_web1',
                    'subscription_details' => ['metadata' => ['order_no' => 'VA-TEST-WEB1']],
                ],
            ],
        ];

        app(PaymentWebhookController::class)->handleInvoicePaymentSucceeded($invoicePaidPayload);

        Queue::assertPushed(SendPaymentReceiptEmail::class, 1);
    }

    public function test_confirmation_recovers_when_webhook_fires_before_order_exists(): void
    {
        Queue::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_race_1']);

        $webhook = app(PaymentWebhookController::class);

        $payload = [
            'id' => 'evt_invoice_paid_race',
            'data' => [
                'object' => [
                    'id' => 'in_race_1',
                    'customer' => 'cus_race_1',
                    'subscription' => 'sub_race_1',
                    'subscription_details' => ['metadata' => ['order_no' => 'VA-TEST-RACE1']],
                ],
            ],
        ];

        // 1) The webhook arrives first, but the order row does not exist yet.
        $this->assertSame(200, $webhook->handleInvoicePaymentSucceeded($payload)->getStatusCode());
        Queue::assertNotPushed(SendPaymentReceiptEmail::class);
        $this->assertDatabaseCount('payment_receipts', 0);

        // 2) The browser redirect then creates the order and confirms the payment.
        $order = $user->orders()->create([
            'order_no' => 'VA-TEST-RACE1',
            'status' => PaymentStatusEnum::Pending->value,
            'sub_total' => '10.00',
            'tax' => 0,
            'total' => '10.00',
            'currency' => 'gbp',
            'payment_method' => 'stripe',
        ]);

        $confirmed = app(\App\Services\OrderService::class)->confirmPayment($order, 'in_race_1');

        $this->assertTrue($confirmed);
        $this->assertSame(PaymentStatusEnum::Paid->value, $order->fresh()->status);
        Queue::assertPushed(SendPaymentReceiptEmail::class, 1);
        $this->assertDatabaseHas('payment_receipts', [
            'stripe_invoice_id' => 'in_race_1',
            'order_id' => $order->id,
        ]);

        // 3) The (late) webhook must not send a second receipt for the same invoice.
        $webhook->handleInvoicePaymentSucceeded($payload);
        Queue::assertPushed(SendPaymentReceiptEmail::class, 1);
        $this->assertDatabaseCount('payment_receipts', 1);
    }

    public function test_confirm_payment_is_idempotent_per_invoice_but_allows_renewals(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $order = $user->orders()->create([
            'order_no' => 'VA-TEST-IDEM1',
            'status' => PaymentStatusEnum::Pending->value,
            'sub_total' => '10.00',
            'tax' => 0,
            'total' => '10.00',
            'currency' => 'gbp',
            'payment_method' => 'stripe',
        ]);

        $service = app(\App\Services\OrderService::class);

        // First payment for this invoice: confirms and dispatches once.
        $this->assertTrue($service->confirmPayment($order, 'in_idem_1'));
        // Same invoice again (webhook + redirect): idempotent.
        $this->assertFalse($service->confirmPayment($order, 'in_idem_1'));

        $this->assertSame(PaymentStatusEnum::Paid->value, $order->fresh()->status);
        Queue::assertPushed(SendPaymentReceiptEmail::class, 1);
        $this->assertDatabaseCount('payment_receipts', 1);

        // A later renewal invoice must still produce its own receipt.
        $this->assertTrue($service->confirmPayment($order, 'in_renew_2'));
        Queue::assertPushed(SendPaymentReceiptEmail::class, 2);
        $this->assertDatabaseCount('payment_receipts', 2);
    }

    public function test_admin_order_details_show_psc_quantity_in_months_not_hours(): void
    {
        $admin = User::factory()->create(['user_type' => UserTypeEnum::SUPER_ADMIN->value]);

        $user = User::factory()->create();
        $plan = $this->createPremiumPlan();
        $this->subscribeUserToPlan($user, $plan);
        $pscType = $this->createPscType();

        $order = $user->orders()->create([
            'order_no' => 'VA-TEST-ADM1',
            'status' => PaymentStatusEnum::Paid->value,
            'sub_total' => '5.00',
            'tax' => 0,
            'total' => '5.00',
            'currency' => 'gbp',
            'payment_method' => 'stripe',
        ]);

        OrderDetail::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'ref_no' => 'VA-TEST-ADM1-1',
            'name' => 'Person with Significant Control - ' . $pscType->name,
            'product_type' => ProductTypeEnum::PSC->value,
            'product_id' => $plan->product_id,
            'quantity' => 1,
            'price' => '5.00',
            'sub_total' => '5.00',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Type: Company PSC')
            ->assertSee('1 Month')
            ->assertDontSee('Total Duration');
    }
}