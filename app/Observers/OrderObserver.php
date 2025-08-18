<?php

namespace App\Observers;

use App\Models\Order;
use App\Mail\NewOrderEmail;
use App\Enums\ProductTypeEnum;
use App\Enums\SubscriptionTypeEnum;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {

        try{
           
            Mail::to($order->user->email)->queue(new NewOrderEmail($order));
            /*
            Mail::send('emails.CustomerOrderEmail', $data, function ($message) use ($order) {
                $message->to($order->user->email, $order->user->name)
                    ->from('info@ninuk.co.uk', 'Ninuk Virtual Address')
                    ->subject('Order Confirmation - ' . $order->order_no);
            });
            */
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            //Log::error('Failed to send order confirmation email: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
