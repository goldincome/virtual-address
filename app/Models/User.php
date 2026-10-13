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
}
