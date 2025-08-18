<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureSetting extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * 
     */
    protected $casts = [
        'status' => 'boolean',
    ];
    
}
