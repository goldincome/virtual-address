<?php

namespace App\Models;

use App\Models\Order;
use App\Enums\ProductTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected $fillable = ['name', 'order_id', 'product_id', 'quantity', 'price',
    'product_type', 'plan_id', 'features', 'plan', 'sub_total', 'booked_date',
    'all_booked_time', 'ref_no', 'discounts', 'user_id'];
    
    protected $casts = [
        'product_type' => ProductTypeEnum::class,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function myPlan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id', 'id');
    }
    public function isVirtualAddress()
    {
        return $this->product_type->value === ProductTypeEnum::VIRTUAL_ADDRESS->value;
    }
    public function isMeetingRoom()
    {
        return $this->product_type->value === ProductTypeEnum::MEETING_ROOM->value;
    }

   public function isConferenceRoom()
    {
        return $this->product_type->value === ProductTypeEnum::CONFERENCE_ROOM->value;
    }
}
