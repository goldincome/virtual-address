<?php

namespace App\Models;

use App\Enums\MailTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailSetting extends Model
{
    
    protected $fillable = [
        'plan_id',
        'name',
        'mail_type',
        'price',
        'status',
        'interval',
        'stripe_product_id',
        'stripe_price_id',
        'stripe_price_name'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mail_type' => MailTypeEnum::class,
        'status' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get the plan that owns the mail setting.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
