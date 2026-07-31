<?php

namespace Tests\Feature\Auth;

use App\Jobs\SendWelcomeEmail;
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
        ]);

        Queue::assertPushed(SendWelcomeEmail::class, function ($job) {
            return $job->user->email === 'test@example.com';
        });
    }
}
