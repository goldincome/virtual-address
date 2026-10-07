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
        return $this->getMedia(self::PRIMARY_IMAGE)->first() ? $this->getMedia(self::PRIMARY_IMAGE)->first()->getUrl() : '';
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

    /**
     * Only packages with mail forwarding or mail scanning can add a
     * company and Persons with Significant Control (PSC).
     */
    public function allowsCompanyPsc(): bool
    {
        return $this->mailSettings()
            ->where('status', true)
            ->whereIn('mail_type', ['scanned', 'forwarded'])
            ->exists();
    }

    /**
     * Build per-plan feature display groups for the plan comparison cards.
     *
     * When a plan's features are a superset of another (lower) plan's, they are
     * split into a shared "base" plan and the exclusive remainder, so cards can
     * show "All Features in {base plan}" instead of repeating the base plan's
     * feature list.
     *
     * @return array<string, array{base: Plan|null, exclusive: \Illuminate\Support\Collection}>
     */
    public static function featureGroupsForListing(\Illuminate\Database\Eloquent\Collection $plans): array
    {
        $all = $plans->values();
        $groups = [];

        foreach ($all as $plan) {
            $planSlugs = $plan->features
                ->map(fn ($feature) => optional($feature->featureSetting)->slug)
                ->filter()
                ->values();

            $base = $all
                ->filter(fn ($candidate) => $candidate->id !== $plan->id)
                ->filter(function ($candidate) use ($planSlugs) {
                    $candidateSlugs = $candidate->features
                        ->map(fn ($feature) => optional($feature->featureSetting)->slug)
                        ->filter()
                        ->values();

                    return $candidateSlugs->isNotEmpty()
                        && $candidateSlugs->every(fn ($slug) => $planSlugs->contains($slug));
                })
                ->sortBy(fn ($candidate) => $candidate->level ?? 0)
                ->last();

            $groups[$plan->id] = [
                'base' => $base,
                'exclusive' => $base
                    ? $plan->features->filter(function ($feature) use ($base) {
                        $baseSlugs = $base->features
                            ->map(fn ($feature) => optional($feature->featureSetting)->slug)
                            ->filter()
                            ->values();

                        return ! $baseSlugs->contains(optional($feature->featureSetting)->slug);
                    })
                    : $plan->features,
            ];
        }

        return $groups;
    }
}
