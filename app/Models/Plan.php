<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Plan extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'price',
        'yearly_monthly_price',
        'signup_fee',
        'currency',
        'trial_period',
        'trial_interval',
        'invoice_period',
        'invoice_interval',
        'grace_period',
        'grace_interval',
        'prorate_day',
        'prorate_period',
        'prorate_extend_due',
        'active_subscribers_limit',
        'stripe_product_id',
        'stripe_price_id_monthly',
        'stripe_price_id_yearly',
        'stripe_coupon_id',
        'stripe_promotion_code_id',
        'discount_percent',
        'discount_amount',
        'discount_duration_in_months',
        'payment_price_id',
        'interval',
        'level',
    ];
    
    public $registerMediaConversionsUsingModelInstance = true;
    const PRIMARY_IMAGE = 'plan_primary_image';
    const ADDITIONAL_IMAGES = 'plan_additional_images';
    const IMAGE_FOLDER = 'virtual_address';


    public function getPrimaryImageAttribute()
    {
        return $this->getMedia(self::PRIMARY_IMAGE)->first()->getUrl() ?? '';
    }

    public function getAdditionalImagesAttribute()
    {
        return $this->getMedia(self::ADDITIONAL_IMAGES);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::PRIMARY_IMAGE, self::IMAGE_FOLDER)
             ->singleFile()
             ->useDisk('public');

        $this->addMediaCollection(self::ADDITIONAL_IMAGES, self::IMAGE_FOLDER)
             ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->performOnCollections('plan_primary_image', 'plan_additional_images')
            ->width(368)
            ->height(232)
            ->queued(); // Important: this makes it run in background;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function features()
    {
        return $this->hasMany(Feature::class);
    }

    public function mailSettings()
    {
        return $this->hasMany(MailSetting::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function planRoomDiscounts()
    {
        return $this->hasMany(PlanRoomDiscount::class);
    }
}
