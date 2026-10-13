<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class SubscriptionCancelledEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string $stage 'requested' (cancellation scheduled) or 'ended' (subscription over).
     */
    public function __construct(
        protected User $user,
        protected ?Carbon $endsAt = null,
        protected string $stage = 'requested',
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->stage === 'ended'
                ? 'Your Charlton Virtual Office Subscription Has Ended'
                : 'Your Charlton Virtual Office Subscription Has Been Cancelled',
            from: new Address(config('app.admin_email'), 'Charlton Virtual Office'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.SubscriptionCancelledEmail',
            with: [
                'user' => $this->user,
                'endsAt' => $this->endsAt,
                'stage' => $this->stage,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
