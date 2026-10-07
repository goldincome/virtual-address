<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\FeatureSetting;
use App\Models\MailSetting;
use App\Models\Plan;
use App\Models\Product;
use App\Enums\ProductTypeEnum;
use App\Enums\MailTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PremiumPackageSeeder extends Seeder
{
    /**
     * Placeholder prices. Update these in the admin panel afterwards –
     * the Plan/Observer will archive and recreate the Stripe prices.
     */
    const PLAN_MONTHLY_PRICE = '35.00';
    const PLAN_YEARLY_PRICE = '350.00';
    const MAIL_ADDON_PRICE = '1.00';

    /**
     * Feature settings used exclusively by the Premium Package.
     * Upserted by slug so the seeder can be re-run safely.
     */
    const PREMIUM_FEATURE_SETTINGS = [
        ['slug' => 'registered-office-address', 'name' => 'Registered Office Address', 'icon' => 'fas fa-building'],
        ['slug' => 'directors-service-address', 'name' => "Director's Service Address", 'icon' => 'fas fa-user-tie'],
        ['slug' => 'psc-service-address', 'name' => 'PSC Service Address', 'icon' => 'fas fa-user-shield'],
        ['slug' => 'public-trading-address', 'name' => 'Public Trading & Business Address', 'icon' => 'fas fa-store'],
        ['slug' => 'mail-acceptance-royal-mail', 'name' => 'Complete Mail & Parcel Acceptance', 'icon' => 'fas fa-inbox'],
        ['slug' => 'priority-digital-mailroom', 'name' => 'Priority Digital Mailroom', 'icon' => 'fas fa-envelope-open-text'],
        ['slug' => 'free-mail-collection', 'name' => 'Free In-Person Mail Collection', 'icon' => 'fas fa-hand-holding'],
        ['slug' => 'post-termination-mail-hold', 'name' => '30-Day Post-Termination Mail Hold', 'icon' => 'fas fa-hourglass-half'],
    ];

    /**
     * Feature slugs that must never be attached to the Premium Package.
     */
    const REMOVED_FEATURE_SLUGS = [
        'high-speed-wi-fi',
        'capacity-up-to-8-people',
        'comfortable-seating',
        'power-outlets-accessible',
        'whiteboard-markers',
        'air-conditioned',
        'meeting-conference-room-access',
        'inclusive-mail-forwarding',
    ];

    /**
     * Fallback feature slugs if no "Package Two" plan exists to clone from.
     */
    const FALLBACK_FEATURE_SLUGS = [
        'prestigious-business-address',
        'mail-receiving',
        'weekly-mail-forwarding',
        'mail-scanning-50-pagesmonth',
        'daily-mail-forwarding',
        'mail-forwarding-pay-per-use',
        'mail-scanning-pay-per-use',
        'meeting-room-access-discounted',
        'local-phone-number-optional',
        'dedicated-phone-line-answering',
    ];

    public function run(): void
    {
        $product = Product::where('type', ProductTypeEnum::VIRTUAL_ADDRESS->value)->first();
        if (!$product) {
            $this->command->error('No Virtual Address product found. Please create one before running this seeder.');
            return;
        }

        try {
            DB::beginTransaction();

            // 1. Create the Premium Package plan (PlanObserver creates the Stripe product + prices).
            //    Create via the product relation so product_id is set (it is not fillable on Plan).
            $plan = Plan::where('slug', 'premium-package')->first();
            if (!$plan) {
                $discountAmount = (self::PLAN_MONTHLY_PRICE * 12) - self::PLAN_YEARLY_PRICE;
                $discountPercent = (float) number_format(($discountAmount / (self::PLAN_MONTHLY_PRICE * 12)) * 100, 2);

                $plan = $product->plans()->create([
                    'name' => 'Premium Package',
                    'slug' => 'premium-package',
                    'description' => $this->premiumDescription(),
                    'is_active' => true,
                    'price' => self::PLAN_MONTHLY_PRICE,
                    'yearly_monthly_price' => self::PLAN_YEARLY_PRICE,
                    'signup_fee' => '0.00',
                    'currency' => config('cashier.currency'),
                    'trial_period' => 0,
                    'trial_interval' => 'month',
                    'invoice_period' => 1,
                    'invoice_interval' => 'month',
                    'grace_period' => 0,
                    'grace_interval' => 'day',
                    'level' => (Plan::max('level') ?? 0) + 1,
                    'interval' => 'month',
                    'discount_duration_in_months' => 10,
                    'discount_amount' => $discountAmount,
                    'discount_percent' => $discountPercent,
                ]);
            }

            // 2. Upsert premium feature settings (by slug, idempotent)
            foreach (self::PREMIUM_FEATURE_SETTINGS as $setting) {
                FeatureSetting::firstOrCreate(
                    ['slug' => $setting['slug']],
                    [
                        'name' => $setting['name'],
                        'icon' => $setting['icon'],
                        'description' => $setting['name'],
                        'status' => true,
                    ]
                );
            }

            // 3. Attach features to the plan (clone Package Two, then add premium ones)
            $this->attachFeatures($plan, $product);

            // 4. Enable Mail Scanning + Mail Forwarding (MailSettingObserver creates metered Stripe prices)
            $this->attachMailSettings($plan);

            DB::commit();

            $this->command->info('Premium Package seeded. Set final prices in the admin panel (PSC Prices, Plans, Mail Prices).');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->command->error('Failed to seed Premium Package: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function attachFeatures(Plan $plan, Product $product): void
    {
        $sourceSlugs = $this->findPackageTwoFeatureSlugs();
        if (empty($sourceSlugs)) {
            $sourceSlugs = self::FALLBACK_FEATURE_SLUGS;
        }

        $slugs = array_merge($sourceSlugs, array_column(self::PREMIUM_FEATURE_SETTINGS, 'slug'));
        $slugs = array_values(array_diff($slugs, self::REMOVED_FEATURE_SLUGS));

        $sortOrder = 1;
        foreach ($slugs as $slug) {
            $settings = FeatureSetting::where('slug', $slug)->first();
            if (!$settings) {
                continue;
            }
            Feature::firstOrCreate(
                ['plan_id' => $plan->id, 'feature_setting_id' => $settings->id],
                [
                    'description' => $settings->name,
                    'product_id' => $product->id,
                    'is_activated' => true,
                    'sort_order' => $sortOrder++,
                ]
            );
        }
    }

    protected function findPackageTwoFeatureSlugs(): array
    {
        $packageTwo = Plan::where('name', 'like', '%Two%')
            ->orWhere('name', 'like', '%2%')
            ->orderBy('level')
            ->first();

        if (!$packageTwo) {
            return [];
        }

        return $packageTwo->features()
            ->with('featureSetting')
            ->get()
            ->pluck('featureSetting.slug')
            ->filter()
            ->values()
            ->all();
    }

    protected function attachMailSettings(Plan $plan): void
    {
        $mailSettings = [
            [
                'mail_type' => MailTypeEnum::Scanned->value,
                'name' => 'Mail Scanning – Premium Package',
                'stripe_price_name' => 'premium-package-mail-scanning',
            ],
            [
                'mail_type' => MailTypeEnum::Forwarded->value,
                'name' => 'Mail Forwarding – Premium Package',
                'stripe_price_name' => 'premium-package-mail-forwarding',
            ],
        ];

        foreach ($mailSettings as $setting) {
            MailSetting::firstOrCreate(
                ['plan_id' => $plan->id, 'mail_type' => $setting['mail_type']],
                [
                    'name' => $setting['name'],
                    'price' => self::MAIL_ADDON_PRICE,
                    'status' => true,
                    'interval' => 'month',
                    'stripe_price_name' => $setting['stripe_price_name'],
                ]
            );
        }
    }

    protected function premiumDescription(): string
    {
        return 'Our all-inclusive Premium Package: registered office address, directors and PSC service '
            . 'addresses, priority digital mailroom, free mail collection and a 30-day post-termination '
            . 'mail hold.';
    }
}