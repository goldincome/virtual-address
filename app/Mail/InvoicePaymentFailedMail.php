<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoicePaymentFailedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $invoiceId;
    public $amountDue;
    public $hostedInvoiceUrl;
    public $attemptCount;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $invoiceId = null, $amountDue = null, $hostedInvoiceUrl = null, $attemptCount = null)
    {
        $this->user = $user;
        $this->invoiceId = $invoiceId;
        $this->amountDue = $amountDue;
        $this->hostedInvoiceUrl = $hostedInvoiceUrl;
        $this->attemptCount = $attemptCount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Failed - Action Required',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-payment-failed',
            with: [
                'user' => $this->user,
                'invoiceId' => $this->invoiceId,
                'amountDue' => $this->amountDue,
                'hostedInvoiceUrl' => $this->hostedInvoiceUrl,
                'attemptCount' => $this->attemptCount,
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
