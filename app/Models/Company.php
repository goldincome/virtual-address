<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'user_id',
        'company_name',
        'address',
        'phone',
        'email',
        'description',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function companyPscs(): HasMany
    {
        return $this->hasMany(CompanyPsc::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
