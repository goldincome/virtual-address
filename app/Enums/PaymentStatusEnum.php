<?php
namespace App\Enums;

enum PaymentStatusEnum: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Cancelled => 'Cancelled',
            self::Failed => 'Failed',
            self::Refunded => 'Refunded',
            self::Paid => 'Paid',
        };
    }

    public function colorClass(): string
    {
        return match($this) {
            self::Pending => 'bg-yellow-100 text-yellow-800',
            self::Paid => 'bg-green-100 text-green-800',
            self::Failed => 'bg-red-100 text-red-800',
            self::Refunded => 'bg-gray-100 text-gray-800',
        };
    }
    
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}