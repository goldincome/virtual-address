<?php

namespace Database\Seeders;

use App\Models\CompanyType;
use Illuminate\Database\Seeder;

class CompanyTypeSeeder extends Seeder
{
    /**
     * Default prices for an additional company slot. Update the CompanyType
     * record afterwards – CompanyTypeObserver archives the old Stripe prices
     * and recreates new ones.
     */
    const DEFAULT_MONTHLY_PRICE = '9.99';
    const DEFAULT_YEARLY_PRICE = '59.00';

    public function run(): void
    {
        $companyType = CompanyType::firstOrCreate(
            ['name' => 'Additional Company'],
            [
                'description' => 'An additional company slot beyond the one included with your plan.',
                'price_monthly' => self::DEFAULT_MONTHLY_PRICE,
                'price_yearly' => self::DEFAULT_YEARLY_PRICE,
                'status' => true,
            ]
        );

        $companyType->fill([
            'price_monthly' => self::DEFAULT_MONTHLY_PRICE,
            'price_yearly' => self::DEFAULT_YEARLY_PRICE,
        ]);
        if ($companyType->isDirty()) {
            $companyType->save();
        }

        $this->command->info('Default Company type seeded (additional company slot). Stripe product/prices sync automatically via the queue.');
    }
}
