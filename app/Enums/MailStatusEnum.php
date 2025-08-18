<?php
namespace App\Enums;



enum MailStatusEnum: string
{
    case Scanned = 'scanned';
    case Forwarded = 'forwarded';
    case Pending = 'pending';
    case Paid = 'paid';



    public function label(): string
    {
        return match ($this) {
            self::Scanned => 'Scanned',
            self::Forwarded=> 'Forwarded',
            self::Pending => 'Pending',
            self::Paid => 'Paid & Processing',
        };
      
    }
     public function colorClass(): string
    {
        return match($this) {
            self::Pending => 'bg-yellow-100 text-yellow-800',
            self::Paid => 'bg-blue-100 text-blue-800',
            self::Scanned, self::Forwarded => 'bg-green-100 text-green-800',
        };
    }

    public static function toArray(): array
    {
      return array_column(self::cases(), 'value');
    }


}