<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\SubscriptionCancelledEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendSubscriptionCancelledEmail implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    public ?Carbon $endsAt;

    public string $stage;

    public function __construct(User $user, ?Carbon $endsAt = null, string $stage = 'requested')
    {
        $this->user = $user;
        $this->endsAt = $endsAt;
        $this->stage = $stage;
    }

    public function handle(): void
    {
        try {
            Mail::to($this->user->email)->send(
                new SubscriptionCancelledEmail($this->user, $this->endsAt, $this->stage)
            );
        } catch (\Exception $e) {
            Log::error('Job SendSubscriptionCancelledEmail failed for user ' . $this->user->id . ': ' . $e->getMessage());
        }
    }

    public function uniqueId(): string
    {
        return 'subscription-cancelled-' . $this->stage . '-' . $this->user->id;
    }
}