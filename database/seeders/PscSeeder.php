<?php

namespace Database\Seeders;

use App\Models\PscType;
use Illuminate\Database\Seeder;

class PscSeeder extends Seeder
{
    /**
     * Placeholder prices. Edit them in the admin panel (PSC Prices) afterwards –
     * PscTypeObserver archives the old Stripe prices and recreates new ones.
     */
    const DEFAULT_MONTHLY_PRICE = '4.99';
    const DEFAULT_YEARLY_PRICE = '39.99';

    public function run(): void
    {
        $pscType = PscType::firstOrCreate(
            ['name' => 'Individual PSC'],
            [
                'description' => 'Persons with Significant Control (PSC) registered against a company.',
                'price_monthly' => self::DEFAULT_MONTHLY_PRICE,
                'price_yearly' => self::DEFAULT_YEARLY_PRICE,
                'status' => true,
            ]
        );

        // Keep an existing "Individual PSC" type in sync with the new prices.
        // Model-level update (not a query-builder update) so the observer fires
        // and Stripe prices are re-synced; it only runs when a price changed.
        $pscType->fill([
            'price_monthly' => self::DEFAULT_MONTHLY_PRICE,
            'price_yearly' => self::DEFAULT_YEARLY_PRICE,
        ]);
        if ($pscType->isDirty()) {
            $pscType->save();
        }

        $this->command->info('Default PSC type seeded. Set the final price in the admin panel (PSC Prices) – Stripe syncs automatically via the queue.');
    }
}