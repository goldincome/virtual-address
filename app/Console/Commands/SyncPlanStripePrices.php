<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;
use Stripe\StripeClient;
use Throwable;

class SyncPlanStripePrices extends Command
{
    protected $signature = 'plans:sync-stripe
        {plan? : Plan id or slug}
        {--all : Process every plan}
        {--check : Only verify the stored Stripe IDs, do not change anything}
        {--force : Recreate prices even when the stored ones are still valid}';

    protected $description = 'Ensure a plan has a valid Stripe product and monthly/yearly prices, saving the IDs back to the database.';

    protected StripeClient $stripe;

    public function handle(): int
    {
        $this->stripe = new StripeClient(config('cashier.secret'));

        $plans = $this->resolvePlans();
        if ($plans->isEmpty()) {
            $this->error('No matching plans found.');

            return self::FAILURE;
        }

        $failed = 0;
        foreach ($plans as $plan) {
            try {
                $this->processPlan($plan);
            } catch (Throwable $e) {
                $failed++;
                $this->error(sprintf('  ! %s (id %s): %s', $plan->name, $plan->id, $e->getMessage()));
            }
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function resolvePlans(): \Illuminate\Support\Collection
    {
        if ($this->option('all')) {
            return Plan::orderBy('level')->get();
        }

        $key = $this->argument('plan');
        if (!$key) {
            return collect();
        }

        return Plan::where('id', $key)
            ->orWhere('slug', $key)
            ->orWhere('name', $key)
            ->get();
    }

    private function processPlan(Plan $plan): void
    {
        $this->line(sprintf('<info>%s</info> (id %s) — %s', $plan->name, $plan->id, $plan->slug));

        $checkOnly = $this->option('check');

        $productId = $this->resolveProduct($plan, $checkOnly);
        $monthlyId = $this->resolvePrice($plan, 'monthly', $plan->stripe_price_id_monthly, $plan->price, 'month', $productId, $checkOnly);
        $yearlyId = $this->resolvePrice($plan, 'yearly', $plan->stripe_price_id_yearly, $plan->yearly_monthly_price, 'year', $productId, $checkOnly);

        $this->syncMailSettings($plan, $checkOnly);

        if ($checkOnly) {
            return;
        }

        $updates = [];
        if ($productId !== $plan->stripe_product_id) {
            $updates['stripe_product_id'] = $productId;
        }
        if ($monthlyId !== $plan->stripe_price_id_monthly) {
            $updates['stripe_price_id_monthly'] = $monthlyId;
        }
        if ($yearlyId !== $plan->stripe_price_id_yearly) {
            $updates['stripe_price_id_yearly'] = $yearlyId;
        }

        if ($updates) {
            $plan->withoutEvents(fn () => $plan->update($updates));
            foreach ($updates as $field => $value) {
                $this->line(sprintf('  → saved %s = %s', $field, $value));
            }
        } else {
            $this->line('  = Stripe IDs already valid, nothing to save.');
        }
    }

    private function syncMailSettings(Plan $plan, bool $checkOnly): void
    {
        $settings = $plan->mailSettings;

        foreach ($settings as $setting) {
            $name = $setting->name ?? ucfirst($setting->mail_type->value) . ' add-on';
            $this->line(sprintf('  mail: %s', $name));

            if ($setting->stripe_product_id) {
                try {
                    $this->stripe->products->retrieve($setting->stripe_product_id);
                    $productId = $setting->stripe_product_id;
                    $this->line('    product valid: ' . $productId);
                } catch (Throwable $e) {
                    $this->warn('    product missing on Stripe: ' . $setting->stripe_product_id);
                    $productId = null;
                }
            } else {
                $this->warn('    no stripe_product_id stored');
                $productId = null;
            }

            if (!$productId) {
                if ($checkOnly) {
                    throw new \RuntimeException('Mail add-on product is missing.');
                }
                $product = $this->stripe->products->create([
                    'name' => $name,
                    'type' => 'service',
                ]);
                $productId = $product->id;
                $this->line('    + created product ' . $productId);
            }

            $priceId = $this->resolveMeteredPrice($setting, $productId, $checkOnly);

            if ($checkOnly) {
                continue;
            }

            $updates = [];
            if ($productId !== $setting->stripe_product_id) {
                $updates['stripe_product_id'] = $productId;
            }
            if ($priceId !== $setting->stripe_price_id) {
                $updates['stripe_price_id'] = $priceId;
            }

            if ($updates) {
                $setting->withoutEvents(fn () => $setting->update($updates));
                foreach ($updates as $field => $value) {
                    $this->line(sprintf('    → saved mail %s = %s', $field, $value));
                }
            }
        }
    }

    private function resolveMeteredPrice(\App\Models\MailSetting $setting, string $productId, bool $checkOnly): ?string
    {
        $amount = (int) round(((float) $setting->price) * 100);

        if ($amount <= 0) {
            $this->line('    mail price: amount is 0, skipped');

            return null;
        }

        if ($setting->stripe_price_id && !$this->option('force')) {
            try {
                $price = $this->stripe->prices->retrieve($setting->stripe_price_id);
                if ($price->active) {
                    $this->line('    mail price valid: ' . $price->id);

                    return $price->id;
                }
                $this->warn('    mail price inactive: ' . $setting->stripe_price_id);
            } catch (Throwable $e) {
                $this->warn('    mail price missing on Stripe: ' . $setting->stripe_price_id);
            }
        }

        if ($checkOnly) {
            throw new \RuntimeException('Mail add-on price is missing.');
        }

        if ($setting->stripe_price_id) {
            try {
                $this->stripe->prices->update($setting->stripe_price_id, ['active' => false]);
                $this->line('    - archived old mail price ' . $setting->stripe_price_id);
            } catch (Throwable $e) {
                // Old price does not exist; nothing to archive.
            }
        }

        $params = [
            'product' => $productId,
            'unit_amount' => $amount,
            'currency' => config('cashier.currency'),
            'billing_scheme' => 'per_unit',
            'recurring' => [
                'interval' => $setting->interval ?: 'month',
                'usage_type' => 'metered',
            ],
        ];

        if ($setting->stripe_price_name) {
            $params['lookup_key'] = $setting->stripe_price_name;
        }

        try {
            $price = $this->stripe->prices->create($params);
        } catch (Throwable $e) {
            // A stale lookup_key can be reused once the old price is gone;
            // retry without it rather than failing.
            unset($params['lookup_key']);
            $price = $this->stripe->prices->create($params);
        }

        $this->line('    + created mail price ' . $price->id);

        return $price->id;
    }

    private function resolveProduct(Plan $plan, bool $checkOnly): string
    {
        if ($plan->stripe_product_id) {
            try {
                $product = $this->stripe->products->retrieve($plan->stripe_product_id);
                $this->line('  product valid: ' . $product->id);

                return $product->id;
            } catch (Throwable $e) {
                $this->warn('  product missing on Stripe: ' . $plan->stripe_product_id);
            }
        } else {
            $this->warn('  no stripe_product_id stored');
        }

        if ($checkOnly) {
            throw new \RuntimeException('Stripe product is missing.');
        }

        $product = $this->stripe->products->create([
            'name' => $plan->name,
            'description' => $plan->description,
        ]);
        $this->line('  + created product ' . $product->id);

        return $product->id;
    }

    private function resolvePrice(Plan $plan, string $label, ?string $storedId, $amount, string $interval, string $productId, bool $checkOnly): ?string
    {
        $amountInMinorUnits = (int) round(((float) $amount) * 100);

        if ($amountInMinorUnits <= 0) {
            $this->line(sprintf('  %s: amount is 0, skipped', $label));

            return null;
        }

        if ($storedId && !$this->option('force')) {
            try {
                $price = $this->stripe->prices->retrieve($storedId);
                if ($price->active) {
                    $this->line(sprintf('  %s price valid: %s', $label, $price->id));

                    return $price->id;
                }
                $this->warn(sprintf('  %s price inactive: %s', $label, $storedId));
            } catch (Throwable $e) {
                $this->warn(sprintf('  %s price missing on Stripe: %s', $label, $storedId));
            }
        } elseif ($storedId) {
            $this->warn(sprintf('  %s price forced recreate (was %s)', $label, $storedId));
        } else {
            $this->warn(sprintf('  %s: no price id stored', $label));
        }

        if ($checkOnly) {
            throw new \RuntimeException(sprintf('%s price is missing.', $label));
        }

        if ($storedId) {
            try {
                $this->stripe->prices->update($storedId, ['active' => false]);
                $this->line('  - archived old ' . $label . ' price ' . $storedId);
            } catch (Throwable $e) {
                // Old price does not exist; nothing to archive.
            }
        }

        $price = $this->stripe->prices->create([
            'product' => $productId,
            'unit_amount' => $amountInMinorUnits,
            'currency' => $plan->currency ?: config('cashier.currency'),
            'recurring' => ['interval' => $interval],
        ]);
        $this->line(sprintf('  + created %s price %s (%s %s/%s)', $label, $price->id, number_format($amountInMinorUnits / 100, 2), strtoupper($plan->currency ?: config('cashier.currency')), $interval));

        return $price->id;
    }
}
