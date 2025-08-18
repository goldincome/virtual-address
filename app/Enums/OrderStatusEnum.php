<?php
namespace App\Enums;

enum OrderStatusEnum: string
{

    case PENDING = 'pending';
    case CANCELED = 'canceled';
    case PAID = 'paid';
    case DELIVERED = 'delivered';

    

    public function customerLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Processing',
            self::CANCELED => 'Order Canceled',
            self::PAID => 'Paid',
            self::DELIVERED => 'Completed',
        };
    }

    public function adminLabel(): string
    {
        return match ($this) {
            self::PENDING=> 'Processing Payment',
            self::CANCELED => 'Order Canceled',
            self::PAID => 'Payment Made',
            self::DELIVERED => 'Completed',
        };
    }
   
}
