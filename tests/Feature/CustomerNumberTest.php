<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Gloudemans\Shoppingcart\Facades\Cart;
use Tests\TestCase;

class CustomerNumberTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_get_a_unique_eight_digit_customer_number(): void
    {
        $users = User::factory()->count(5)->create();

        foreach ($users as $user) {
            $this->assertMatchesRegularExpression('/^\d{8}$/', $user->customer_number);
        }

        $this->assertCount(5, $users->pluck('customer_number')->unique());
    }

    public function test_registration_assigns_a_customer_number(): void
    {
        $this->get('/');
        Cart::add('va_plan_1', 'Test Plan', 1, 100.00, 0, ['type' => 'virtual_address']);

        $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '+1234567890',
            'email' => 'customer-number@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'customer-number@example.com')->first();

        $this->assertNotNull($user);
        $this->assertMatchesRegularExpression('/^\d{8}$/', $user->customer_number);
    }

    public function test_generated_customer_number_is_not_already_taken(): void
    {
        $existing = User::factory()->create();
        $existing->forceFill(['customer_number' => '12345678'])->save();

        $number = User::generateUniqueCustomerNumber();

        $this->assertMatchesRegularExpression('/^\d{8}$/', $number);
        $this->assertNotSame('12345678', $number);
    }
}
