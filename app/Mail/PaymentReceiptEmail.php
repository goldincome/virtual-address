<?php

namespace App\Mail;

use App\Models\Order;
use App\Services\ServiceAgreementService;
use Illuminate\Bus\Queueable;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Enums\ProductTypeEnum;
use App\Enums\SubscriptionTypeEnum;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;

class PaymentReceiptEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(protected Order $order)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Confirmation - ' . $this->order->order_no,
            from: new Address(config('app.admin_email'), 'Charlton Virtual Office'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $data = $this->sharedViewData();
        return new Content(
            view: 'emails.CustomerOrderEmail',
            with: $data,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $data = $this->sharedViewData();

        $invoicePdf = Pdf::loadView('pdf.invoice', $data)->output();

        $agreementData = $this->agreementData();
        $agreementPdf = Pdf::loadView('pdf.service-agreement', $agreementData)->output();

        return [
            Attachment::fromData(
                fn () => $invoicePdf,
                'invoice-' . $this->order->order_no . '.pdf'
            )->withMime('application/pdf'),
            Attachment::fromData(
                fn () => $agreementPdf,
                'service-agreement-' . $this->order->order_no . '.pdf'
            )->withMime('application/pdf'),
        ];
    }

    /**
     * Shared data required by both the email view and the invoice PDF view.
     */
    protected function sharedViewData(): array
    {
        $subscriptionDetail = $this->order->hasSubscription()
            ? $this->order->orderDetails()->where('product_type', ProductTypeEnum::VIRTUAL_ADDRESS->value)->latest()->first()
            : null;

        return [
            'order' => $this->order,
            'jsonPlan' => json_decode($subscriptionDetail?->plan),
            'subscriptionType' => SubscriptionTypeEnum::class,
        ];
    }

    /**
     * Data required to fill the virtual office service agreement PDF.
     */
    protected function agreementData(): array
    {
        $data = $this->sharedViewData();

        return app(ServiceAgreementService::class)->forOrder($this->order, $data['jsonPlan']);
    }
}