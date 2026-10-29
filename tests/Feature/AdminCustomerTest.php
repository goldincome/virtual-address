<?php

namespace Tests\Feature;

use App\Models\User;
use App\Enums\UserTypeEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_customers_list(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserTypeEnum::ADMIN->value,
        ]);

        $customer = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.customers.index'));

        $response->assertOk();
        $response->assertSee('John Doe');
        $response->assertSee('john@example.com');
    }

    public function test_admin_can_view_customer_detail(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserTypeEnum::ADMIN->value,
        ]);

        $customer = User::factory()->create([
            'name' => 'Jane Smith',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.customers.show', $customer));

        $response->assertOk();
        $response->assertSee('Jane Smith');
    }

    public function test_admin_can_update_customer(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserTypeEnum::ADMIN->value,
        ]);

        $customer = User::factory()->create([
            'name' => 'Old Name',
        ]);

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.customers.update', $customer), [
                'name' => 'New Name',
                'email' => $customer->email,
                'status' => 'active',
            ]);

        $response->assertRedirect(route('admin.customers.show', $customer));
        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'name' => 'New Name',
        ]);
    }

    public function test_admin_can_suspend_and_activate_customer(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserTypeEnum::ADMIN->value,
        ]);

        $customer = User::factory()->create([
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.customers.suspend', $customer));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'status' => 'suspended',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.customers.activate', $customer));

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_filter_customers_by_plan(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserTypeEnum::ADMIN->value,
        ]);

        $plan = \App\Models\Plan::withoutEvents(fn () => \App\Models\Plan::create([
            'name' => 'Premium Plan',
            'slug' => 'premium-plan',
            'price' => 35.00,
            'currency' => 'GBP',
            'level' => 2,
            'is_active' => true,
        ]));

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.customers.index', ['plan_id' => $plan->id]));

        $response->assertOk();
    }

    public function test_admin_can_filter_customers_by_subscription_status(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserTypeEnum::ADMIN->value,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.customers.index', ['subscription_status' => 'none']));

        $response->assertOk();
    }

    public function test_admin_can_export_customers(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserTypeEnum::ADMIN->value,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.customers.export'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }

    public function test_admin_can_view_archived_subscribers(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserTypeEnum::ADMIN->value,
        ]);

        $archivedUser = User::factory()->create([
            'status' => 'archived',
            'name' => 'Archived User',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.customers.archived'));

        $response->assertOk();
        $response->assertSee('Archived User');
    }

    public function test_admin_can_view_and_update_profile(): void
    {
        $admin = User::factory()->create([
            'user_type' => UserTypeEnum::ADMIN->value,
            'name' => 'Old Admin Name',
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route('admin.profile.edit'));

        $response->assertOk();
        $response->assertSee('Old Admin Name');

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.profile.update'), [
                'name' => 'New Admin Name',
                'email' => $admin->email,
            ]);

        $response->assertRedirect(route('admin.profile.edit'));
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'New Admin Name',
        ]);
    }
}
