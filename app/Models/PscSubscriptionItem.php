<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PscSubscriptionItem extends Model
{
    protected $fillable = [
        'user_id',
        'psc_type_id',
        'stripe_subscription_item_id',
        'quantity',
        'interval',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pscType(): BelongsTo
    {
        return $this->belongsTo(PscType::class);
    }
}
