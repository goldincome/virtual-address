<?php

namespace App\Listeners;

use App\Jobs\SendWelcomeEmail;
use Illuminate\Auth\Events\Registered;

class SendWelcomeEmailListener
{
    public function handle(Registered $event): void
    {
        SendWelcomeEmail::dispatch($event->user);
    }
}
