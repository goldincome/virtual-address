<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\Product;
use App\Models\Company;
use App\Models\PscType;
use App\Models\MailSetting;
use App\Enums\MailTypeEnum;
use App\Jobs\SendPaymentReceiptEmail;
use App\Enums\ProductTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Jobs\SendSubscriptionCancelledEmail;
use App\Services\SubscriptionChangeService;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\SubscriptionItem;
use Illuminate\Support\Str;
use Stripe\ApiRequestor;
use Tests\Support\FakeStripeHttpClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionChangeTest extends TestCase
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
        parent::tearDown();
    }

    protected function fakeStripe(callable $responder): FakeStripeHttpClient
    {
        $client = new FakeStripeHttpClient($responder);
        ApiRequestor::setHttpClient($client);

        return $client;
    }

    protected function makeProduct(): Product
    {
        return Product::create([
            'name' => 'Virtual Address',
            'type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
            'intro' => 'A professional London business address',
            'price' => 0,
            'is_active' => true,
        ]);
    }

    protected function makePlan(Product $product, string $name, string $slug, int $level, string $monthlyPriceId): Plan
    {
        return Plan::withoutEvents(fn () => $product->plans()->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $name . ' plan',
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
            'level' => $level,
            'stripe_price_id_monthly' => $monthlyPriceId,
        ]));
    }

    protected function subscribe(User $user, string $priceId, array $attrs = []): \App\Models\PlanSubscription
    {
        $subscription = $user->subscriptions()->create(array_merge([
            'type' => 'default',
            'stripe_id' => 'sub_test_' . Str::random(8),
            'stripe_status' => 'active',
            'stripe_price' => $priceId,
            'quantity' => 1,
        ], $attrs));

        SubscriptionItem::withoutEvents(fn () => SubscriptionItem::create([
            'subscription_id' => $subscription->id,
            'stripe_id' => 'si_test_' . Str::random(8),
            'stripe_product' => 'prod_test',
            'stripe_price' => $priceId,
            'quantity' => 1,
        ]));

        return $subscription;
    }

    public function test_has_scheduled_change_only_for_future_scheduled_changes(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct();
        $plan = $this->makePlan($product, 'Base', 'base', 1, 'price_base');

        $subscription = $this->subscribe($user, 'price_base');
        $this->assertFalse($subscription->hasScheduledChange());

        $subscription->forceFill([
            'subscription_schedule_id' => 'sched_1',
            'scheduled_change_at' => Carbon::now()->addDays(10),
        ])->save();

        $this->assertTrue($subscription->fresh()->hasScheduledChange());

        // A change in the past is no longer "scheduled".
        $subscription->forceFill(['scheduled_change_at' => Carbon::now()->subDay()])->save();
        $this->assertFalse($subscription->fresh()->hasScheduledChange());
    }

    public function test_upgrade_applies_immediately_and_creates_a_paid_order(): void
    {
        Queue::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_upgrade']);
        $product = $this->makeProduct();
        $base = $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');
        $premium = $this->makePlan($product, 'Premium', 'premium', 2, 'price_premium_monthly');

        $subscription = $this->subscribe($user, 'price_base_monthly');

        $stripe = $this->fakeStripe(function ($method, $path) {
            if ($method === 'post' && str_contains($path, '/v1/subscriptions/sub_test')) {
                return FakeStripeHttpClient::json([
                    'id' => 'sub_test',
                    'status' => 'active',
                    'latest_invoice' => [
                        'id' => 'in_upgrade',
                        'status' => 'paid',
                        'amount_due' => 0,
                        'hosted_invoice_url' => null,
                    ],
                ]);
            }
            if ($method === 'post' && str_contains($path, '/v1/invoices/in_upgrade')) {
                return FakeStripeHttpClient::json(['id' => 'in_upgrade', 'status' => 'paid']);
            }
            return FakeStripeHttpClient::json(['ok' => true]);
        });

        $result = app(SubscriptionChangeService::class)->changePlan($user, $premium, 'monthly');

        $this->assertSame('upgrade', $result['type']);
        $this->assertNotNull($result['order']);
        $this->assertNull($result['redirect_url']);

        // A single order was created for the plan change and marked paid.
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('orders', [
            'id' => $result['order']->id,
            'status' => PaymentStatusEnum::Paid->value,
            'payment_method' => 'stripe',
        ]);
        $this->assertDatabaseHas('order_details', [
            'order_id' => $result['order']->id,
            'plan_id' => $premium->id,
            'product_type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
        ]);

        // The Stripe subscription item was swapped in-place (no new subscription).
        $this->assertDatabaseCount('subscriptions', 1);
        $this->assertSame(
            'price_premium_monthly',
            $subscription->fresh()->items()->first()->stripe_price
        );
        $this->assertSame($premium->id, $subscription->fresh()->plan->id);

        Queue::assertPushed(SendPaymentReceiptEmail::class, 1);

        // Only the plan item was changed; proration was invoiced immediately.
        $update = $stripe->requestsFor('post', '/v1/subscriptions/sub_test')[0];
        $this->assertSame('always_invoice', $update['params']['proration_behavior']);
        $this->assertSame('price_premium_monthly', $update['params']['items'][0]['price']);
        $this->assertArrayHasKey('id', $update['params']['items'][0]);
    }

    public function test_upgrade_returns_hosted_invoice_url_when_payment_is_incomplete(): void
    {
        Queue::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_upgrade2']);
        $product = $this->makeProduct();
        $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');
        $premium = $this->makePlan($product, 'Premium', 'premium', 2, 'price_premium_monthly');

        $this->subscribe($user, 'price_base_monthly');

        $this->fakeStripe(function ($method, $path) {
            if ($method === 'post' && str_contains($path, '/v1/subscriptions/sub_test')) {
                return FakeStripeHttpClient::json([
                    'id' => 'sub_test',
                    'status' => 'active',
                    'latest_invoice' => [
                        'id' => 'in_open',
                        'status' => 'open',
                        'amount_due' => 1500,
                        'hosted_invoice_url' => 'https://invoice.stripe.com/i/in_open',
                    ],
                ]);
            }
            if ($method === 'post' && str_contains($path, '/v1/invoices/in_open')) {
                return FakeStripeHttpClient::json(['id' => 'in_open', 'status' => 'open']);
            }
            return FakeStripeHttpClient::json(['ok' => true]);
        });

        $result = app(SubscriptionChangeService::class)->changePlan($user, $premium, 'monthly');

        $this->assertSame('upgrade', $result['type']);
        $this->assertSame('https://invoice.stripe.com/i/in_open', $result['redirect_url']);
        // Not paid yet.
        $this->assertDatabaseHas('orders', ['status' => PaymentStatusEnum::Pending->value]);
        Queue::assertNotPushed(SendPaymentReceiptEmail::class);
    }

    public function test_downgrade_is_scheduled_at_period_end_without_charging(): void
    {
        Queue::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_downgrade']);
        $product = $this->makeProduct();
        $base = $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');
        $premium = $this->makePlan($product, 'Premium', 'premium', 2, 'price_premium_monthly');

        $subscription = $this->subscribe($user, 'price_premium_monthly');

        $periodEnd = Carbon::now()->addDays(20)->timestamp;

        $this->fakeStripe(function ($method, $path) use ($periodEnd) {
            if ($method === 'get' && str_contains($path, '/v1/subscriptions/sub_test')) {
                return FakeStripeHttpClient::json([
                    'id' => 'sub_test',
                    'status' => 'active',
                    'current_period_end' => $periodEnd,
                    'items' => ['data' => [[
                        'id' => 'si_plan',
                        'quantity' => 1,
                        'price' => ['id' => 'price_premium_monthly', 'recurring' => ['usage_type' => 'licensed']],
                    ]]],
                ]);
            }
            if ($method === 'post' && $path === '/v1/subscription_schedules') {
                return FakeStripeHttpClient::json([
                    'id' => 'sched_1',
                    'status' => 'active',
                    'phases' => [[
                        'start_date' => Carbon::now()->timestamp,
                        'end_date' => $periodEnd,
                    ]],
                ]);
            }
            if ($method === 'post' && str_contains($path, '/v1/subscription_schedules/sched_1')) {
                return FakeStripeHttpClient::json(['id' => 'sched_1', 'status' => 'active']);
            }
            return FakeStripeHttpClient::json(['ok' => true]);
        });

        $result = app(SubscriptionChangeService::class)->changePlan($user, $base, 'monthly');

        $this->assertSame('downgrade', $result['type']);
        $this->assertNull($result['order']);
        $this->assertNull($result['redirect_url']);

        // No order is created for a downgrade (no charge now).
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('subscriptions', 1);

        $subscription->refresh();
        $this->assertSame('sched_1', $subscription->subscription_schedule_id);
        $this->assertSame($base->id, $subscription->scheduled_plan_id);
        $this->assertSame('downgrade', $subscription->scheduled_change_type);
        $this->assertTrue($subscription->hasScheduledChange());
        $this->assertSame($base->id, $subscription->scheduledPlan->id);
    }

    public function test_cancel_releases_schedule_and_cancels_at_period_end(): void
    {
        Queue::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_cancel']);
        $product = $this->makeProduct();
        $base = $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');
        $subscription = $this->subscribe($user, 'price_base_monthly', [
            'subscription_schedule_id' => 'sched_x',
            'scheduled_plan_id' => $base->id,
            'scheduled_change_at' => Carbon::now()->addDays(15),
            'scheduled_change_type' => 'downgrade',
        ]);

        $periodEnd = Carbon::now()->addDays(15)->timestamp;

        $this->fakeStripe(function ($method, $path) use ($periodEnd) {
            if ($method === 'post' && str_contains($path, '/v1/subscription_schedules/sched_x/release')) {
                return FakeStripeHttpClient::json(['id' => 'sched_x', 'status' => 'released']);
            }
            if ($method === 'post' && str_contains($path, '/v1/subscriptions/sub_test')) {
                return FakeStripeHttpClient::json([
                    'id' => 'sub_test',
                    'status' => 'active',
                    'current_period_end' => $periodEnd,
                ]);
            }
            return FakeStripeHttpClient::json(['ok' => true]);
        });

        app(SubscriptionChangeService::class)->cancel($user);

        $subscription->refresh();
        $this->assertTrue($subscription->canceled());
        $this->assertTrue($subscription->onGracePeriod());
        $this->assertNotNull($subscription->ends_at);
        // Scheduled change was cleared.
        $this->assertNull($subscription->subscription_schedule_id);
        $this->assertNull($subscription->scheduled_plan_id);

        Queue::assertPushed(SendSubscriptionCancelledEmail::class, function ($job) {
            return $job->stage === 'requested';
        });
    }

    public function test_resume_within_grace_period_reactivates_subscription(): void
    {
        Queue::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_resume']);
        $product = $this->makeProduct();
        $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');
        $this->subscribe($user, 'price_base_monthly', [
            'ends_at' => Carbon::now()->addDays(10),
        ]);

        $this->fakeStripe(function ($method, $path) {
            if ($method === 'post' && str_contains($path, '/v1/subscriptions/sub_test')) {
                return FakeStripeHttpClient::json([
                    'id' => 'sub_test',
                    'status' => 'active',
                    'current_period_end' => Carbon::now()->addDays(30)->timestamp,
                ]);
            }
            return FakeStripeHttpClient::json(['ok' => true]);
        });

        $this->assertTrue(app(SubscriptionChangeService::class)->resume($user));

        $subscription = $user->subscription('default');
        $this->assertNull($subscription->ends_at);
        $this->assertFalse($subscription->onGracePeriod());
    }

    public function test_store_guard_routes_an_existing_subscriber_through_change_service(): void
    {
        Queue::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_guard']);
        $product = $this->makeProduct();
        $base = $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');
        $premium = $this->makePlan($product, 'Premium', 'premium', 2, 'price_premium_monthly');

        $this->subscribe($user, 'price_base_monthly');

        $this->fakeStripe(function ($method, $path) {
            if ($method === 'post' && str_contains($path, '/v1/subscriptions/sub_test')) {
                return FakeStripeHttpClient::json([
                    'id' => 'sub_test',
                    'status' => 'active',
                    'latest_invoice' => ['id' => 'in_guard', 'status' => 'paid', 'amount_due' => 0],
                ]);
            }
            if ($method === 'post' && str_contains($path, '/v1/invoices/in_guard')) {
                return FakeStripeHttpClient::json(['id' => 'in_guard', 'status' => 'paid']);
            }
            return FakeStripeHttpClient::json(['ok' => true]);
        });

        $this->actingAs($user)
            ->post(route('virtual-address.store', ['plan_id' => $premium->id]), [
                'subscription_type' => 'monthly',
            ])
            ->assertRedirect(route('checkout.success', \App\Models\Order::first()->order_no));

        // Nothing was added to the shopping cart; the change was applied in place.
        $this->assertTrue(Cart::content()->isEmpty());
        $this->assertDatabaseCount('subscriptions', 1);
        $this->assertSame('price_premium_monthly', $user->subscription('default')->fresh()->items()->first()->stripe_price);
    }

    public function test_store_guard_blocks_buying_the_same_plan_again(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_same']);
        $product = $this->makeProduct();
        $base = $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');

        $this->subscribe($user, 'price_base_monthly');

        $this->actingAs($user)
            ->post(route('virtual-address.store', ['plan_id' => $base->id]), [
                'subscription_type' => 'monthly',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertTrue(Cart::content()->isEmpty());
        $this->assertDatabaseCount('subscriptions', 1);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_duplicate_subscription_report_command_lists_affected_users(): void
    {
        $product = $this->makeProduct();
        $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');

        $duplicateUser = User::factory()->create(['stripe_id' => 'cus_dup']);
        $this->subscribe($duplicateUser, 'price_base_monthly');
        $this->subscribe($duplicateUser, 'price_base_monthly');

        $cleanUser = User::factory()->create(['stripe_id' => 'cus_clean']);
        $this->subscribe($cleanUser, 'price_base_monthly');

        $this->artisan('subscriptions:find-duplicates')
            ->expectsOutputToContain('User #' . $duplicateUser->id)
            ->assertExitCode(0);

        $this->artisan('subscriptions:find-duplicates', ['--user' => $cleanUser->id])
            ->expectsOutputToContain('No users with duplicate live subscriptions were found.')
            ->assertExitCode(0);
    }

    public function test_downgrade_releases_an_orphaned_stripe_schedule_before_scheduling(): void
    {
        Queue::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_orphan']);
        $product = $this->makeProduct();
        $base = $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');
        $premium = $this->makePlan($product, 'Premium', 'premium', 2, 'price_premium_monthly');

        $subscription = $this->subscribe($user, 'price_premium_monthly');
        // Local row has no schedule id, but Stripe still has one attached
        // (a previous attempt failed before saving locally).
        $this->assertNull($subscription->subscription_schedule_id);

        $periodEnd = Carbon::now()->addDays(20)->timestamp;

        $stripe = $this->fakeStripe(function ($method, $path) use ($periodEnd) {
            if ($method === 'get' && str_contains($path, '/v1/subscriptions/sub_test')) {
                return FakeStripeHttpClient::json([
                    'id' => 'sub_test',
                    'status' => 'active',
                    'schedule' => 'sched_orphan',
                    'current_period_end' => $periodEnd,
                    'items' => ['data' => [[
                        'id' => 'si_plan',
                        'quantity' => 1,
                        'price' => ['id' => 'price_premium_monthly', 'recurring' => ['usage_type' => 'licensed']],
                    ]]],
                ]);
            }
            if ($method === 'post' && str_contains($path, '/v1/subscription_schedules/sched_orphan/release')) {
                return FakeStripeHttpClient::json(['id' => 'sched_orphan', 'status' => 'released']);
            }
            if ($method === 'post' && $path === '/v1/subscription_schedules') {
                return FakeStripeHttpClient::json([
                    'id' => 'sched_new',
                    'status' => 'active',
                    'phases' => [['start_date' => Carbon::now()->timestamp, 'end_date' => $periodEnd]],
                ]);
            }
            if ($method === 'post' && str_contains($path, '/v1/subscription_schedules/sched_new')) {
                return FakeStripeHttpClient::json(['id' => 'sched_new', 'status' => 'active']);
            }
            return FakeStripeHttpClient::json(['ok' => true]);
        });

        $result = app(SubscriptionChangeService::class)->changePlan($user, $base, 'monthly');

        $this->assertSame('downgrade', $result['type']);
        // The orphaned schedule was released first.
        $this->assertNotEmpty($stripe->requestsFor('post', '/v1/subscription_schedules/sched_orphan/release'));
        // A fresh schedule was then created.
        $this->assertNotEmpty($stripe->requestsFor('post', '/v1/subscription_schedules'));
        $this->assertSame('sched_new', $subscription->fresh()->subscription_schedule_id);
    }

    public function test_dashboard_shows_cancel_and_change_actions_for_active_subscriber(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_dash']);
        $product = $this->makeProduct();
        $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');
        $this->subscribe($user, 'price_base_monthly');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Cancel Subscription')
            ->assertSee('Change Plan');
    }

    public function test_dashboard_shows_resume_for_grace_period_subscription(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_dash2']);
        $product = $this->makeProduct();
        $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');
        $this->subscribe($user, 'price_base_monthly', [
            'ends_at' => Carbon::now()->addDays(7),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Resume Subscription');
    }

    public function test_plan_page_shows_downgrade_action_for_lower_plan(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_plan']);
        $product = $this->makeProduct();
        $base = $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');
        $this->makePlan($product, 'Premium', 'premium', 2, 'price_premium_monthly');
        $this->subscribe($user, 'price_premium_monthly');

        $this->actingAs($user)
            ->get(route('virtual-address.show', $base->slug))
            ->assertOk()
            ->assertSee('Downgrade To this Plan');
    }
}
