<?php

namespace App\Models;

use App\Models\Plan;
use App\Models\Feature;
use Illuminate\Support\Str;
use App\Enums\ProductTypeEnum;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia;

    public $registerMediaConversionsUsingModelInstance = true;

    protected $fillable = [
        'name', 'type', 'price', 'slug', 'intro', 'description', 'currency', 
        'is_active', 'main_product_image', 'is_completed'
    ];

    protected $casts = ['is_active' => 'boolean',
        'type' => ProductTypeEnum::class,
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];  
  
    const CONFERENCE_PRIMARY_IMAGE = 'conference_primary_image';
    const CONFERENCE_ADDITIONAL_IMAGES = 'conference_additional_images';
    const CONFERENCE_IMAGE_FOLDER = 'conference_room';
    
    const MEETING_PRIMARY_IMAGE = 'meeting_room_primary_image';
    const MEETING_ADDITIONAL_IMAGES = 'meeting_room_additional_images';
    const MEETING_IMAGE_FOLDER = 'meeting_room';
     // Automatically set slug (if you add a slug column to products table)
     protected static function boot()
     {
         parent::boot();
         static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
             }
         });
     }
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEETING_PRIMARY_IMAGE, self::MEETING_IMAGE_FOLDER)
             ->singleFile()
             ->useDisk('public');

        $this->addMediaCollection(self::MEETING_ADDITIONAL_IMAGES, self::MEETING_IMAGE_FOLDER)
             ->useDisk('public');

        $this->addMediaCollection(self::CONFERENCE_PRIMARY_IMAGE, self::CONFERENCE_IMAGE_FOLDER)
             ->singleFile()
             ->useDisk('public');

        $this->addMediaCollection(self::CONFERENCE_ADDITIONAL_IMAGES, self::CONFERENCE_IMAGE_FOLDER)
             ->useDisk('public');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->performOnCollections(self::MEETING_PRIMARY_IMAGE, self::MEETING_ADDITIONAL_IMAGES)
            ->width(368)
            ->height(232)
            ->queued(); // Important: this makes it run in background;
        $this->addMediaConversion('thumb')
            ->performOnCollections(self::CONFERENCE_PRIMARY_IMAGE, self::CONFERENCE_ADDITIONAL_IMAGES)
            ->width(368)
            ->height(232)
            ->queued();

    }
    public function getMeetingPrimaryImageAttribute()
    {
        return $this->getMedia(self::MEETING_PRIMARY_IMAGE)->first()->getUrl() ?? '';
    }
    public function getConferencePrimaryImageAttribute()
    {
        return $this->getMedia(self::CONFERENCE_PRIMARY_IMAGE)->first()->getUrl() ?? '';
    }
    public function getVirtualAddressAttribute()
    {
        return $this->where('type', ProductTypeEnum::VIRTUAL_ADDRESS)->first();
    }
    public function getMeetingRoomsAttribute()
    {
        return self::where('type', ProductTypeEnum::MEETING_ROOM)->get();
    }
    public function getConferenceRoomsAttribute()
    {
        return self::where('type', ProductTypeEnum::CONFERENCE_ROOM)->get();
    }
    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function type()
    {
        return $this->belongsTo(ProductTypeEnum::class);
    }

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class);
    }

    public function planRoomDiscounts()
    {
        return $this->hasMany(PlanRoomDiscount::class);
    }
}
