<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentMethodEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function makeOrder(User $user, string $orderNo = '1000000001'): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'order_no' => $orderNo,
            'total' => 100.00,
            'sub_total' => 100.00,
            'tax' => 0.00,
            'discount' => 0.00,
            'payment_method' => PaymentMethodEnum::Bank->value,
            'currency' => 'GBP',
            'status' => OrderStatusEnum::PAID->value,
        ]);
    }

    public function test_customer_can_download_their_invoice_pdf(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOrder($user);

        $response = $this
            ->actingAs($user)
            ->get(route('invoices.download', $order->order_no));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString(
            'invoice-' . $order->order_no . '.pdf',
            $response->headers->get('content-disposition')
        );
    }

    public function test_customer_cannot_download_another_users_invoice(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = $this->makeOrder($owner);

        $response = $this
            ->actingAs($other)
            ->get(route('invoices.download', $order->order_no));

        $response->assertRedirect();
    }

    public function test_customer_can_download_their_service_agreement_pdf(): void
    {
        $user = User::factory()->create();
        $order = $this->makeOrder($user);

        $response = $this
            ->actingAs($user)
            ->get(route('invoices.agreement', $order->order_no));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString(
            'service-agreement-' . $order->order_no . '.pdf',
            $response->headers->get('content-disposition')
        );
    }
}
