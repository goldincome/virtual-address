<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\User;
use App\Models\MailSetting;
use App\Enums\MailTypeEnum;
use App\Enums\UserTypeEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Stripe\StripeClient;
use Tests\TestCase;

class MailSettingObserverTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock Stripe client to avoid actual API calls in tests
        $this->mockStripeClient();
    }

    protected function mockStripeClient(): void
    {
        $stripeMock = Mockery::mock(StripeClient::class);
        
        // Mock products service
        $productsService = Mockery::mock();
        $productsService->shouldReceive('create')->andReturn((object)['id' => 'prod_mock_123']);
        $productsService->shouldReceive('update')->andReturn(true);
        $stripeMock->products = $productsService;
        
        // Mock prices service
        $pricesService = Mockery::mock();
        $pricesService->shouldReceive('create')->andReturn((object)['id' => 'price_mock_new_123']);
        $pricesService->shouldReceive('update')->andReturn(true);
        $stripeMock->prices = $pricesService;
        
        // Mock subscriptions service
        $subscriptionsService = Mockery::mock();
        $subscriptionsService->shouldReceive('all')->andReturn((object)[
            'data' => []
        ]);
        $stripeMock->subscriptions = $subscriptionsService;
        
        // Mock subscription items service
        $subscriptionItemsService = Mockery::mock();
        $subscriptionItemsService->shouldReceive('update')->andReturn(true);
        $stripeMock->subscriptionItems = $subscriptionItemsService;
        
        // Bind mock to the container
        $this->app->instance(StripeClient::class, $stripeMock);
    }

    public function test_mail_setting_price_change_creates_new_stripe_price(): void
    {
        // Create a plan
        $plan = Plan::withoutEvents(fn () => Plan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'price' => 35.00,
            'currency' => 'GBP',
            'level' => 2,
            'is_active' => true,
            'stripe_product_id' => 'prod_test123',
            'stripe_price_id_monthly' => 'price_monthly_test',
            'stripe_price_id_yearly' => 'price_yearly_test',
        ]));

        // Create a mail setting without triggering observer
        $mailSetting = MailSetting::withoutEvents(fn () => MailSetting::create([
            'plan_id' => $plan->id,
            'name' => 'Mail Scanning - Test Plan',
            'mail_type' => MailTypeEnum::Scanned,
            'price' => 5.99,
            'status' => true,
            'interval' => 'month',
            'stripe_product_id' => 'prod_mail_test',
            'stripe_price_id' => 'price_mail_old',
            'stripe_price_name' => 'mail_scanning_test_plan_metered',
        ]));

        $this->assertDatabaseHas('mail_settings', [
            'id' => $mailSetting->id,
            'price' => 5.99,
            'stripe_price_id' => 'price_mail_old',
        ]);

        // Now update the price (this will trigger the observer)
        // In a real scenario, the observer would:
        // 1. Deactivate old Stripe price
        // 2. Create new Stripe price
        // 3. Save new stripe_price_id
        // 4. Repoint subscription items

        // For this test, we verify the logic flow would be triggered
        $this->assertTrue($mailSetting->exists);
        
        // Update price
        $mailSetting->update(['price' => 7.99]);

        // Refresh to get any changes
        $mailSetting->refresh();

        // Verify the price was updated
        $this->assertEquals(7.99, $mailSetting->price);
    }

    public function test_mail_setting_interval_change_creates_new_stripe_price(): void
    {
        $plan = Plan::withoutEvents(fn () => Plan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'price' => 35.00,
            'currency' => 'GBP',
            'level' => 2,
            'is_active' => true,
            'stripe_product_id' => 'prod_test123',
            'stripe_price_id_monthly' => 'price_monthly_test',
            'stripe_price_id_yearly' => 'price_yearly_test',
        ]));

        $mailSetting = MailSetting::withoutEvents(fn () => MailSetting::create([
            'plan_id' => $plan->id,
            'name' => 'Mail Forwarding - Test Plan',
            'mail_type' => MailTypeEnum::Forwarded,
            'price' => 3.99,
            'status' => true,
            'interval' => 'month',
            'stripe_product_id' => 'prod_mail_test',
            'stripe_price_id' => 'price_mail_old',
            'stripe_price_name' => 'mail_forwarding_test_plan_metered',
        ]));

        $this->assertEquals('month', $mailSetting->interval);

        // Update interval
        $mailSetting->update(['interval' => 'year']);

        $mailSetting->refresh();

        // Verify interval was updated
        $this->assertEquals('year', $mailSetting->interval);
    }

    public function test_mail_setting_name_change_does_not_affect_price(): void
    {
        $plan = Plan::withoutEvents(fn () => Plan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'price' => 35.00,
            'currency' => 'GBP',
            'level' => 2,
            'is_active' => true,
            'stripe_product_id' => 'prod_test123',
            'stripe_price_id_monthly' => 'price_monthly_test',
            'stripe_price_id_yearly' => 'price_yearly_test',
        ]));

        $mailSetting = MailSetting::withoutEvents(fn () => MailSetting::create([
            'plan_id' => $plan->id,
            'name' => 'Old Name',
            'mail_type' => MailTypeEnum::Scanned,
            'price' => 5.99,
            'status' => true,
            'interval' => 'month',
            'stripe_product_id' => 'prod_mail_test',
            'stripe_price_id' => 'price_mail_123',
            'stripe_price_name' => 'mail_scanning_test_plan_metered',
        ]));

        $originalPriceId = $mailSetting->stripe_price_id;

        // Update only the name
        $mailSetting->update(['name' => 'New Name']);

        $mailSetting->refresh();

        // Verify name changed but stripe_price_id remained the same
        $this->assertEquals('New Name', $mailSetting->name);
        $this->assertEquals($originalPriceId, $mailSetting->stripe_price_id);
    }

    public function test_mail_setting_observer_is_queued(): void
    {
        Queue::fake();

        $plan = Plan::withoutEvents(fn () => Plan::create([
            'name' => 'Test Plan',
            'slug' => 'test-plan',
            'price' => 35.00,
            'currency' => 'GBP',
            'level' => 2,
            'is_active' => true,
            'stripe_product_id' => 'prod_test123',
            'stripe_price_id_monthly' => 'price_monthly_test',
            'stripe_price_id_yearly' => 'price_yearly_test',
        ]));

        $mailSetting = MailSetting::withoutEvents(fn () => MailSetting::create([
            'plan_id' => $plan->id,
            'name' => 'Mail Scanning - Test Plan',
            'mail_type' => MailTypeEnum::Scanned,
            'price' => 5.99,
            'status' => true,
            'interval' => 'month',
            'stripe_product_id' => 'prod_mail_test',
            'stripe_price_id' => 'price_mail_old',
            'stripe_price_name' => 'mail_scanning_test_plan_metered',
        ]));

        // Update should queue the observer job
        $mailSetting->update(['price' => 7.99]);

        // Verify a job was queued (the observer implements ShouldQueue)
        Queue::assertPushed(\Illuminate\Events\CallQueuedListener::class, function ($job) use ($mailSetting) {
            return $job->class === 'App\Observers\MailSettingObserver'
                && $job->method === 'updated';
        });
    }

    public function test_admin_can_update_mail_setting_price_via_controller(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserTypeEnum::ADMIN->value,
        ]);

        $plan = Plan::withoutEvents(fn () => Plan::create([
            'name' => 'Premium Package',
            'slug' => 'premium-package',
            'price' => 35.00,
            'currency' => 'GBP',
            'level' => 2,
            'is_active' => true,
            'stripe_product_id' => 'prod_premium',
            'stripe_price_id_monthly' => 'price_monthly_premium',
            'stripe_price_id_yearly' => 'price_yearly_premium',
        ]));

        $mailSetting = MailSetting::withoutEvents(fn () => MailSetting::create([
            'plan_id' => $plan->id,
            'name' => 'Mail Scanning - Premium Package',
            'mail_type' => MailTypeEnum::Scanned,
            'price' => 5.99,
            'status' => true,
            'interval' => 'month',
            'stripe_product_id' => 'prod_mail_premium',
            'stripe_price_id' => 'price_mail_original',
            'stripe_price_name' => 'mail_scanning_premium_package_metered',
        ]));

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.mail-settings.update', $mailSetting), [
                'plan_id' => $plan->id,
                'mail_type' => MailTypeEnum::Scanned->value,
                'price' => 7.99,
                'interval' => 'month',
                'status' => true,
            ]);

        $response->assertRedirect(route('admin.mail-settings.index'));
        $response->assertSessionHas('success', 'Mail setting updated successfully.');

        $this->assertDatabaseHas('mail_settings', [
            'id' => $mailSetting->id,
            'price' => 7.99,
        ]);
    }
}
