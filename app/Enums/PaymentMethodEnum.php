<?php
namespace App\Enums;



enum PaymentMethodEnum: string
{

    case Stripe = 'stripe';
    case PayPal = 'paypal';
    case DirectDebit = 'direct_debit';
    case Bank = 'bank';

    public function label(): string
    {
        return match ($this) {
            self::Stripe => 'Stripe',
            self::PayPal=> 'Paypal',
            self::DirectDebit => 'Direct Debit',
            self::Bank => 'Bank Deposit',
        };
      
    }
public static function toArray(): array
  {
    return array_column(self::cases(), 'value');
  }


}