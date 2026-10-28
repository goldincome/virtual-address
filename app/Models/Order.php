<?php

namespace App\Models;

use App\Enums\ProductTypeEnum;
use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo, Relations\HasMany};


class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total',
        'status',
        'sub_total',
        'tax',
        'currency',
        'payment_method',
        'order_no',
        'discount',
        'payment_method_order_id',
        'stripe_invoice_id',
        'prorated_sub_total',
        'prorated_tax',
        'prorated_discount',
        'prorated_total',
        'proration_breakdown',
    ];
    protected $casts = [
        'proration_breakdown' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function hasSubscription()
    {
        return $this->orderDetails()->where('product_type', ProductTypeEnum::VIRTUAL_ADDRESS->value)
            ->exists();
    }
    public function hasPsc()
    {
        return $this->orderDetails()->where('product_type', ProductTypeEnum::PSC->value)->exists();
    }
    public function hasBookingRoom()
    {
        return $this->orderDetails()->where('product_type', ProductTypeEnum::CONFERENCE_ROOM->value)
            ->orWhere('product_type', ProductTypeEnum::MEETING_ROOM->value)
            ->exists();
    }
    public function bookingRoomDetails()
    {
        return $this->orderDetails()->where('product_type', ProductTypeEnum::CONFERENCE_ROOM->value)
            ->orWhere('product_type', ProductTypeEnum::MEETING_ROOM->value);
    }

    /**
     * Order lines for paid Persons with Significant Control slots.
     */
    public function pscDetails()
    {
        return $this->orderDetails->filter(fn (OrderDetail $detail) => $detail->isPsc());
    }
}
