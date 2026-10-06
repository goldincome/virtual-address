<?php

namespace App\Providers;

use App\Models\Plan;
use App\Models\Order;
use App\Models\MailSetting;
use App\Models\PscType;
use App\Observers\PlanObserver;
use App\Observers\OrderObserver;
use App\Observers\PscTypeObserver;
use App\Observers\MailSettingObserver;
use Illuminate\Auth\Events\Registered;
use App\Listeners\SendWelcomeEmailListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendWelcomeEmailListener::class,
        ],
    ];

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
         PscType::observe(PscTypeObserver::class);
    }
}
