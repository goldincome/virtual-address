<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailUsage extends Model
{
     protected $fillable = [
        'user_id',
        'mail_id',
        'price_id',
        'service_name',
        'price',
        'billed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mail()
    {
        return $this->belongsTo(Mail::class);
    }
}
