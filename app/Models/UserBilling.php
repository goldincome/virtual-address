<?php

namespace App\Models;

use Laravel\Cashier\Subscription;
use App\Enums\SubscriptionTypeEnum;
use Illuminate\Database\Eloquent\Model;

class UserBilling extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'billing_address',
        'postal_code',
        'vat_no',
        'acct_holder_name',
        'acct_number',
        'bank_name',
        'sort_code',
        'subscription_type',
        'default',
        'approval_required',
    ];
    protected $casts = [
        'default' => 'boolean',
        'subscription_type' => SubscriptionTypeEnum::class,
    ];
}
