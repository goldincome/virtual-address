<?php

namespace App\Models;

use App\Models\Order;
use App\Enums\ProductTypeEnum;
use App\Enums\SubscriptionTypeEnum;
use App\Services\PscService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function isPsc()
    {
        return $this->product_type->value === ProductTypeEnum::PSC->value;
    }

    /**
     * Unit suffix shown next to the quantity in orders, invoices and emails.
     * Virtual-address and PSC lines are billed on the subscription interval
     * (mon/yr); other lines such as room bookings use hours.
     */
    public function qtyUnitLabel(): string
    {
        if ($this->isVirtualAddress()) {
            $plan = json_decode($this->plan);
            $isYearly = $plan
                && isset($plan->subscription_type)
                && $plan->subscription_type === SubscriptionTypeEnum::YEARLY->value;
            return $isYearly ? ' yr' : ' mon';
        }

        if ($this->isPsc()) {
            $isYearly = app(PscService::class)->intervalForUser($this->user) === 'year';
            return $isYearly ? ' yr' : ' mon';
        }

        return 'hr(s)';
    }
}
