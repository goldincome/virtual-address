<?php
namespace App\Enums;

enum SubscriptionTypeEnum: string
{
    case MONTHLY = 'monthly';
    case YEARLY = 'yearly';

    /**
     * Get all enum cases as an array of values.
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
    
    public function label(): string
    {
        return match ($this) {
            self::MONTHLY => 'Monthly',
            self::YEARLY => 'Annually',
        };
      
    }
}