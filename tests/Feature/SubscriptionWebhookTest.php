<?php

namespace Tests\Feature;

use Carbon\Carbon;
use App\Models\Plan;
use App\Models\User;
use App\Models\Product;
use App\Models\Company;
use App\Models\MailSetting;
use App\Models\Order;
use App\Enums\MailTypeEnum;
use App\Enums\ProductTypeEnum;
use App\Jobs\SendSubscriptionCancelledEmail;
use App\Http\Controllers\Front\PaymentWebhookController;
use Illuminate\Support\Facades\Queue;
use Laravel\Cashier\SubscriptionItem;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function makeProduct(): Product
    {
        return Product::create([
            'name' => 'Virtual Address',
            'type' => ProductTypeEnum::VIRTUAL_ADDRESS->value,
            'intro' => 'Address',
            'price' => 0,
            'is_active' => true,
        ]);
    }

    protected function makePlan(Product $product, string $name, string $slug, int $level, ?string $priceId, bool $withMail = false): Plan
    {
        $plan = Plan::withoutEvents(fn () => $product->plans()->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $name,
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
            'stripe_price_id_monthly' => $priceId,
        ]));

        if ($withMail) {
            MailSetting::withoutEvents(fn () => MailSetting::create([
                'plan_id' => $plan->id,
                'name' => 'Mail Scanning',
                'mail_type' => MailTypeEnum::Scanned->value,
                'price' => '1.00',
                'status' => true,
                'interval' => 'month',
                'stripe_price_name' => $slug . '-mail',
            ]));
        }

        return $plan;
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

    public function test_scheduled_downgrade_transition_clears_state_and_suspends_companies(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_wh_1']);
        $product = $this->makeProduct();
        $base = $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');
        $premium = $this->makePlan($product, 'Premium', 'premium', 2, 'price_premium_monthly', true);

        $subscription = $this->subscribe($user, 'price_premium_monthly', [
            'subscription_schedule_id' => 'sched_1',
            'scheduled_plan_id' => $base->id,
            'scheduled_change_at' => Carbon::now()->addDays(5),
            'scheduled_change_type' => 'downgrade',
        ]);

        $company = $user->companies()->create(['company_name' => 'ABC Ltd', 'status' => 'active']);

        // Stripe performs the phase change and fires customer.subscription.updated
        // with the subscription now on the lower plan.
        $payload = [
            'id' => 'evt_updated_1',
            'data' => [
                'object' => [
                    'id' => $subscription->stripe_id,
                    'customer' => 'cus_wh_1',
                    'status' => 'active',
                    'cancel_at_period_end' => false,
                    'current_period_end' => Carbon::now()->addDays(30)->timestamp,
                    'metadata' => ['type' => 'default'],
                    'items' => ['data' => [[
                        'id' => 'si_new',
                        'quantity' => 1,
                        'price' => ['id' => 'price_base_monthly', 'product' => 'prod_test'],
                    ]]],
                ],
            ],
        ];

        $response = app(PaymentWebhookController::class)->handleCustomerSubscriptionUpdated($payload);

        $this->assertSame(200, $response->getStatusCode());

        $subscription->refresh();
        $this->assertNull($subscription->subscription_schedule_id);
        $this->assertNull($subscription->scheduled_plan_id);
        $this->assertNull($subscription->scheduled_change_at);
        $this->assertNull($subscription->scheduled_change_type);
        $this->assertSame($base->id, $subscription->plan->id);

        // The higher plan's Company/PSC rights end immediately on transition.
        $this->assertSame('suspended', $company->fresh()->status);
    }

    public function test_subscription_deleted_webhook_sends_the_ended_email(): void
    {
        Queue::fake();

        $user = User::factory()->create(['stripe_id' => 'cus_wh_2']);
        $product = $this->makeProduct();
        $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');

        $subscription = $this->subscribe($user, 'price_base_monthly');

        $payload = [
            'id' => 'evt_deleted_1',
            'data' => [
                'object' => [
                    'id' => $subscription->stripe_id,
                    'customer' => 'cus_wh_2',
                    'ended_at' => Carbon::now()->timestamp,
                ],
            ],
        ];

        $response = app(PaymentWebhookController::class)->handleCustomerSubscriptionDeleted($payload);

        $this->assertSame(200, $response->getStatusCode());

        Queue::assertPushed(SendSubscriptionCancelledEmail::class, function ($job) {
            return $job->stage === 'ended';
        });
    }

    public function test_subscription_updated_ignores_when_no_schedule_pending(): void
    {
        $user = User::factory()->create(['stripe_id' => 'cus_wh_3']);
        $product = $this->makeProduct();
        $this->makePlan($product, 'Base', 'base', 1, 'price_base_monthly');

        $subscription = $this->subscribe($user, 'price_base_monthly');

        $company = $user->companies()->create(['company_name' => 'ABC Ltd', 'status' => 'active']);

        $payload = [
            'id' => 'evt_updated_2',
            'data' => [
                'object' => [
                    'id' => $subscription->stripe_id,
                    'customer' => 'cus_wh_3',
                    'status' => 'active',
                    'cancel_at_period_end' => false,
                    'current_period_end' => Carbon::now()->addDays(30)->timestamp,
                    'metadata' => ['type' => 'default'],
                    'items' => ['data' => [[
                        'id' => 'si_x',
                        'quantity' => 1,
                        'price' => ['id' => 'price_base_monthly', 'product' => 'prod_test'],
                    ]]],
                ],
            ],
        ];

        app(PaymentWebhookController::class)->handleCustomerSubscriptionUpdated($payload);

        // Nothing scheduled -> company untouched.
        $this->assertSame('active', $company->fresh()->status);
    }
}
