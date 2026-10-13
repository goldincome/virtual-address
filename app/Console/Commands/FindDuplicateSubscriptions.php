<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\PlanSubscription;

class FindDuplicateSubscriptions extends Command
{
    protected $signature = 'subscriptions:find-duplicates
        {--user= : Limit the report to a single user id}
        {--all : Include subscriptions that have already fully ended}';

    protected $description = 'Report-only: list users who have more than one live Stripe subscription (caused by the old upgrade bug). Makes no changes.';

    public function handle(): int
    {
        $query = PlanSubscription::query()
            ->where('type', 'default')
            ->with('items');

        if (! $this->option('all')) {
            $query->where(function ($query) {
                $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
            });
        }

        if ($userId = $this->option('user')) {
            $query->where('user_id', $userId);
        }

        $subscriptions = $query->orderBy('user_id')->orderBy('created_at')->get();
        $duplicates = $subscriptions->groupBy('user_id')->filter(fn ($group) => $group->count() > 1);

        if ($duplicates->isEmpty()) {
            $this->info('No users with duplicate live subscriptions were found.');

            return self::SUCCESS;
        }

        $this->warn('Found ' . $duplicates->count() . ' user(s) with more than one live subscription.');

        foreach ($duplicates as $userId => $group) {
            $user = User::find($userId);
            $this->newLine();
            $this->line('<fg=yellow>User #' . $userId . '</> ' . ($user?->email ?? '(unknown)') . ' — ' . $group->count() . ' subscription(s):');

            $rows = $group->map(function (PlanSubscription $subscription) {
                $plan = Plan::where('stripe_price_id_monthly', $subscription->stripe_price)
                    ->orWhere('stripe_price_id_yearly', $subscription->stripe_price)
                    ->first();

                return [
                    $subscription->id,
                    $subscription->stripe_id,
                    $subscription->stripe_status,
                    $plan?->name ?? $subscription->stripe_price,
                    $subscription->ends_at?->format('Y-m-d') ?? '—',
                    $subscription->scheduled_plan_id ? 'yes' : '—',
                    $subscription->created_at?->format('Y-m-d'),
                ];
            })->all();

            $this->table(
                ['ID', 'Stripe ID', 'Status', 'Plan / Price', 'Ends At', 'Scheduled', 'Created'],
                $rows
            );
        }

        $this->newLine();
        $this->comment('Review each user in the Stripe dashboard and cancel the unwanted subscription(s) there.');
        $this->comment('For app-side cleanup, use: php artisan orders:confirm-payment (email recovery) and cancel in Stripe so the webhook syncs local state.');

        return self::SUCCESS;
    }
}
