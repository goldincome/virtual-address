<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Enums\ProductTypeEnum;
use App\Enums\SubscriptionTypeEnum;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewOrderEmail extends Mailable
{
    //use Queueable, SerializesModels;
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    protected $order;
    public function __construct( $order)
    {
        $this->order = $order;
    }
  

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation - ' . $this->order->order_no,
            from: new Address(config('app.admin_email'), 'Charlton Virtual Office'),
            
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $subscriptionDetail = $this->order->hasSubscription() 
        ? $this->order->orderDetails()->where('product_type', ProductTypeEnum::VIRTUAL_ADDRESS->value)->latest()->first() 
        : null;
        return new Content(
            view: 'emails.CustomerOrderEmail',
            with: [
                'order' => $this->order,
                'jsonPlan' => json_decode($subscriptionDetail?->plan), 
                'subscriptionType' => SubscriptionTypeEnum::class,
            ],  
            
        );
         
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}