<?php
namespace App\Enums;

enum DiscountTypeEnum: string
{
    case PERCENTAGE = 'percentage';
    case FLAT = 'flat';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Percentage',
            self::FLAT => 'Flat Amount',
        };
    }
  
    public static function toArray(): array
    {
      return array_column(self::cases(), 'value');
    }


}