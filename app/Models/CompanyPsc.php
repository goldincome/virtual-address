<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyPsc extends Model
{
    protected $fillable = [
        'company_id',
        'psc_type_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'status',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function pscType(): BelongsTo
    {
        return $this->belongsTo(PscType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
