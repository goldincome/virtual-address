<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class BillMailUsages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bill:mail-usages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add service usage as invoice items for each user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get all users who have at least one active subscription.
        $users = User::whereHas('subscriptions', function ($query) {
            $query->where('stripe_status', 'active');
        })->get();

        if ($users->isEmpty()) {
            $this->info('No active subscribers found to bill.');
            return Command::SUCCESS;
        }

        $this->info("Found {$users->count()} active subscribers to process.");

        foreach ($users as $user) {
            // Get the specific metered subscription. This assumes a separate subscription named 'mail_services'
            $meteredSubscription = $user->subscription('metered') ?? $user->subscription('default');
  

            if (!$meteredSubscription || !$meteredSubscription->active()) {
                $this->error("User {$user->email} does not have an active metered subscription. Skipping.");
                continue;
            }
            
            $usages = $user->mailUsages()->where('billed', false)->get();

            if ($usages->isEmpty()) {
                continue; // Skip to the next user if there's nothing to bill.
            }

            foreach ($usages as $usage) {
                try {
                    // You need to get  the Stripe Price ID.
                    $priceId = $usage->price_id;

                    if ($priceId) {
                        // Report a single unit of usage to the specific price on the metered subscription.
                        $meteredSubscription->reportUsageFor($priceId, 1);
                        
                        // Mark the usage as billed.
                        $usage->update(['billed' => true]);

                        $this->info("Successfully reported usage for {$user->email} | Service: {$usage->service_name}");
                    }
                    
                } catch (\Exception $e) {
                    $this->error("Failed to bill {$user->email} for {$usage->service_name}. Error: {$e->getMessage()}");
                    // Log the error and continue to the next usage record.
                }
            }
        }

        $this->info('Finished billing mail usages for all applicable users.');
        return Command::SUCCESS;
    }
}