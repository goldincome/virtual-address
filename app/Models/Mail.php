<?php

namespace App\Models;

use App\Enums\MailTypeEnum;
use App\Enums\MailStatusEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mail extends Model
{
        protected $fillable = [
        'user_id',
        'sender_name',
        'mail_type',
        'tracking_number',
        'tracking_url',
        'description',
        'mail_status',
        'price',
        'payment_status',
        'recieved_at',
        'forwarded_at',
        'scanned_at',
        'scan_upload_url'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'mail_type' => MailTypeEnum::class,
        'mail_status' => MailStatusEnum::class,
        'payment_status' => PaymentStatusEnum::class,
        'price' => 'decimal:2',
        'recieved_at' => 'datetime',
        'forwarded_at' => 'datetime',
        'scanned_at' => 'datetime',
    ];

    /**
     * Get the user that owns the mail.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the mail usages associated with the mail.
     */
    public function mailUsages()
    {
        return $this->hasMany(MailUsage::class);        
    }
    
    public function mailUsage()
    {
        return $this->hasOne(MailUsage::class);        
    }
}
