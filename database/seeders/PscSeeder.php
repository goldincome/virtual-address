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
    const DEFAULT_MONTHLY_PRICE = '5.00';
    const DEFAULT_YEARLY_PRICE = '50.00';

    public function run(): void
    {
        PscType::firstOrCreate(
            ['name' => 'Individual PSC'],
            [
                'description' => 'Persons with Significant Control (PSC) registered against a company.',
                'price_monthly' => self::DEFAULT_MONTHLY_PRICE,
                'price_yearly' => self::DEFAULT_YEARLY_PRICE,
                'status' => true,
            ]
        );

        $this->command->info('Default PSC type seeded. Set the final price in the admin panel (PSC Prices) – Stripe syncs automatically via the queue.');
    }
}