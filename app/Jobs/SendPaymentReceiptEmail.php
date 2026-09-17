<?php

namespace App\Jobs;

use App\Models\Order;
use App\Mail\PaymentReceiptEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendPaymentReceiptEmail implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;

    public string $stripeEventId;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order, string $stripeEventId = '')
    {
        $this->order = $order;
        $this->stripeEventId = $stripeEventId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Use send() here, not queue(), because the Job itself is already queued.
            Mail::to($this->order->user->email)->send(new PaymentReceiptEmail($this->order));
        } catch (\Exception $e) {
            Log::error('Job SendPaymentReceiptEmail failed for order ' . $this->order->id . ': ' . $e->getMessage());
            // Optionally, re-throw the exception to make the job retry
            // throw $e;
        }
    }

    /**
     * The unique ID for this job.
     * Keyed by the Stripe event/invoice ID so each payment sends exactly one receipt,
     * even when the same order is charged again on renewal.
     */
    public function uniqueId(): string
    {
        return $this->stripeEventId !== ''
            ? $this->stripeEventId
            : 'admin-approve-' . $this->order->id;
    }
}