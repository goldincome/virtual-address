<?php

namespace App\Jobs;

use App\Models\Order;
use App\Mail\NewOrderEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;


class SendOrderConfirmationEmail implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Use send() here, not queue(), because the Job itself is already queued.
            Mail::to($this->order->user->email)->send(new NewOrderEmail($this->order));
        } catch (\Exception $e) {
            Log::error('Job SendOrderConfirmationEmail failed for order ' . $this->order->id . ': ' . $e->getMessage());
            // Optionally, re-throw the exception to make the job retry
            // throw $e;
        }
    }

    /**
     * The unique ID for this job.
     * This stops duplicate jobs for the same order.
     */
    public function uniqueId(): string
    {
        return $this->order->id;
    }
}
