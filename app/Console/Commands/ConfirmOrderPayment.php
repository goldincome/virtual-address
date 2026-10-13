<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Console\Command;

class ConfirmOrderPayment extends Command
{
    protected $signature = 'orders:confirm-payment
        {order : Order number or id}
        {--invoice= : Stripe invoice id to record for idempotency}
        {--force : Confirm even when the order is already marked paid}';

    protected $description = 'Mark an order paid and (re)send the payment confirmation email. Useful for orders stranded by the webhook/redirect race.';

    public function handle(): int
    {
        $key = $this->argument('order');

        $order = Order::where('order_no', $key)
            ->orWhere('id', is_numeric($key) ? (int) $key : 0)
            ->first();

        if (! $order) {
            $this->error("Order [{$key}] not found.");

            return self::FAILURE;
        }

        $this->line("Order #{$order->id} ({$order->order_no}) status: {$order->status}");

        if ($order->status === \App\Enums\PaymentStatusEnum::Paid->value && ! $this->option('force')) {
            $this->warn('Order is already paid. Use --force to send another confirmation.');

            return self::SUCCESS;
        }

        $invoiceId = $this->option('invoice') ?: null;

        $confirmed = app(OrderService::class)->confirmPayment($order, $invoiceId);

        if ($confirmed) {
            $this->info("Payment confirmed; confirmation email queued for order #{$order->id}.");
            $this->comment('Ensure a queue worker is running (php artisan queue:work) and was restarted after the last deploy.');
        } else {
            $this->warn('This invoice was already confirmed; no email sent (idempotency guard).');
        }

        return self::SUCCESS;
    }
}
