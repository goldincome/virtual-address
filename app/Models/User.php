<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserTypeEnum;
use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable; 
    use Billable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'first_name',
        'last_name',
        'phone',
        'password',
        'user_type',
        'customer_number',
        'status',
        'business_type',
        'business_registration_name',
        'business_trading_name',
        'business_registration_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->customer_number)) {
                $user->customer_number = static::generateUniqueCustomerNumber();
            }
        });
    }

    /**
     * Generate a unique random 8 digit customer number.
     */
    public static function generateUniqueCustomerNumber(): string
    {
        for ($attempt = 0; $attempt < 50; $attempt++) {
            $number = (string) random_int(10000000, 99999999);

            if (! static::where('customer_number', $number)->exists()) {
                return $number;
            }
        }

        throw new \RuntimeException('Unable to generate a unique customer number.');
    }

    public function isAdmin(): bool
    {
        return in_array($this->user_type, [UserTypeEnum::ADMIN->value, UserTypeEnum::SUPER_ADMIN->value]);
    }

    public function isSuperAdmin(): bool
    { 
        return $this->user_type === UserTypeEnum::SUPER_ADMIN->value;
    }

    public function orders(): HasMany
    { 
        return $this->hasMany(Order::class);
    }

    public function orderDetails(): HasMany
    { 
        return $this->hasMany(OrderDetail::class);
    }

    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }
    
    public function mails(): HasMany
    {
        return $this->hasMany(Mail::class);
    }
    public function subscriptions(): HasMany
    {
        return $this->hasMany(PlanSubscription::class, $this->getForeignKey())->orderBy('created_at', 'desc');
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function pscSubscriptionItems(): HasMany
    {
        return $this->hasMany(PscSubscriptionItem::class);
    }

    public function mailUsages()
    {
        return $this->hasMany(MailUsage::class);
    }

    /**
     * Format phone number for Excel export to prevent scientific notation.
     * Normalizes to display format: +44 (0) 2032 474747
     */
    public function getFormattedPhoneForExport(): string
    {
        if (empty($this->phone)) {
            return '';
        }

        $phone = trim((string) $this->phone);
        
        // Remove all spaces, parentheses, and hyphens for normalization
        $digitsOnly = preg_replace('/[\s\(\)\-]/', '', $phone);
        
        // Check if it's a UK number (starts with +44 or 0044 or just digits assuming UK)
        if (preg_match('/^\+?44(\d{10})$/', $digitsOnly, $matches)) {
            // UK mobile/landline: +44 1234 567890 or +44 20 3247 4747
            $number = $matches[1];
            
            // Format based on number type (simplified)
            if (strlen($number) === 10) {
                // Format: +44 (0) XXXX XXXXXX or +44 (0) XX XXXX XXXX
                if (substr($number, 0, 2) === '20' || substr($number, 0, 2) === '11') {
                    // London/Major city: +44 (0) 20 3247 4747
                    return '+44 (0) ' . substr($number, 0, 2) . ' ' . substr($number, 2, 4) . ' ' . substr($number, 6);
                } else {
                    // Other: +44 (0) 1234 567890
                    return '+44 (0) ' . substr($number, 0, 4) . ' ' . substr($number, 4);
                }
            }
            
            // Fallback for UK
            return '+44 (0) ' . $number;
        }
        
        // For non-UK numbers or already formatted numbers, preserve the format
        // but ensure it starts with + to force Excel text mode
        if (!str_starts_with($phone, '+')) {
            return '+' . $phone;
        }
        
        return $phone;
    }
}
