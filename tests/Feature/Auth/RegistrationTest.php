<?php

namespace Tests\Feature\Auth;

use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_and_continue_to_checkout(): void
    {
        $this->get('/');
        Cart::add('va_plan_1', 'Test Plan', 1, 100.00, 0, ['type' => 'virtual_address']);

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '+1234567890',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'business_type' => 'corporate',
            'business_registration_name' => 'Test Registration Name',
            'business_trading_name' => 'Test Trading Name',
            'business_registration_number' => '12345678',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('cart.index', absolute: false));
        $this->get($response->headers->get('Location'))->assertOk();
    }

    public function test_registration_without_cart_redirects_to_plans(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '+1234567890',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('virtual-address.index', absolute: false));
    }

    public function test_welcome_email_is_queued_on_registration(): void
    {
        Queue::fake();

        $this->get('/');
        Cart::add('va_plan_1', 'Test Plan', 1, 100.00, 0, ['type' => 'virtual_address']);

        $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '+1234567890',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'business_type' => 'corporate',
            'business_registration_name' => 'Test Registration Name',
            'business_trading_name' => 'Test Trading Name',
            'business_registration_number' => '12345678',
        ]);

        Queue::assertPushed(SendWelcomeEmail::class, function ($job) {
            return $job->user->email === 'test@example.com';
        });
    }

    public function test_corporate_business_registration_stores_business_details(): void
    {
        $this->get('/');
        Cart::add('va_plan_1', 'Test Plan', 1, 100.00, 0, ['type' => 'virtual_address']);

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '+1234567890',
            'email' => 'corporate@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'business_type' => 'corporate',
            'business_registration_name' => 'Acme Limited',
            'business_trading_name' => 'Acme Trading',
            'business_registration_number' => '12345678',
        ]);

        $response->assertRedirect(route('cart.index', absolute: false));

        $user = User::where('email', 'corporate@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('corporate', $user->business_type);
        $this->assertSame('Acme Limited', $user->business_registration_name);
        $this->assertSame('Acme Trading', $user->business_trading_name);
        $this->assertSame('12345678', $user->business_registration_number);
    }

    public function test_self_employed_registration_stores_business_trading_name(): void
    {
        $this->get('/');
        Cart::add('va_plan_1', 'Test Plan', 1, 100.00, 0, ['type' => 'virtual_address']);

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '+1234567890',
            'email' => 'self-employed@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'business_type' => 'self_employed',
            'business_trading_name' => 'John Smith Trading',
        ]);

        $response->assertRedirect(route('cart.index', absolute: false));

        $user = User::where('email', 'self-employed@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('self_employed', $user->business_type);
        $this->assertSame('John Smith Trading', $user->business_trading_name);
    }

    public function test_corporate_registration_requires_company_registration_number(): void
    {
        $this->get('/');
        Cart::add('va_plan_1', 'Test Plan', 1, 100.00, 0, ['type' => 'virtual_address']);

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '+1234567890',
            'email' => 'corporate-invalid@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'business_type' => 'corporate',
            'business_registration_name' => 'Acme Limited',
            'business_trading_name' => 'Acme Trading',
        ]);

        $response->assertSessionHasErrors('business_registration_number');
        $this->assertGuest();
    }
}
