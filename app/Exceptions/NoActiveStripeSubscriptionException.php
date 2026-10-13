<?php

namespace App\Exceptions;

use RuntimeException;

class NoActiveStripeSubscriptionException extends RuntimeException
{
    public function __construct(string $message = 'You do not have an active Stripe subscription.')
    {
        parent::__construct($message);
    }
}
