<?php

namespace App\Providers;

use App\Models\Plan;
use App\Models\Order;
use App\Models\MailSetting;
use App\Observers\PlanObserver;
use App\Observers\OrderObserver;
use App\Observers\MailSettingObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
         Plan::observe(PlanObserver::class);
         MailSetting::observe(MailSettingObserver::class);
         Order::observe(OrderObserver::class);
    }
}
