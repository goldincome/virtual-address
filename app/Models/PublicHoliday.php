<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicHoliday extends Model
{
    protected $fillable = [
        'title', 'date_from', 'date_to', 'status' ];

    protected $casts = [
        'status' => 'boolean',
        'date_from' => 'datetime',
        'date_to' => 'datetime',
    ];
}
