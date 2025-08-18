<?php

namespace App\Models;

use App\Enums\DiscountTypeEnum;
use Illuminate\Database\Eloquent\Model;

class PlanRoomDiscount extends Model
{
   protected $fillable = [
        'plan_id',
        'product_id',
        'discount_type',
        'discount_value',
        'is_active',
    ];

    protected $casts = [
        'discount_type' => DiscountTypeEnum::class,
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
