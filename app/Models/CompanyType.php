<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompanyType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price_monthly',
        'price_yearly',
        'status',
        'stripe_product_id',
        'stripe_price_id_monthly',
        'stripe_price_id_yearly',
    ];

    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'status' => 'boolean',
    ];

    public function companySubscriptionItems(): HasMany
    {
        return $this->hasMany(CompanySubscriptionItem::class);
    }

    public function priceForInterval(string $interval): float
    {
        return (float) ($interval === 'year' ? $this->price_yearly : $this->price_monthly);
    }

    public function stripePriceIdForInterval(string $interval): ?string
    {
        return $interval === 'year' ? $this->stripe_price_id_yearly : $this->stripe_price_id_monthly;
    }
}
