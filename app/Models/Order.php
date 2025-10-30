<?php

namespace App\Models;

use App\Enums\ProductTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo, Relations\HasMany};


class Order extends Model
{
    protected $fillable = ['user_id', 'total', 'status', 'sub_total', 'tax', 
    'currency', 'payment_method', 'order_no', 'discount'];

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
}
