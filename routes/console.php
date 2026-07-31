<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('queue:work --stop-when-empty --tries=3 --timeout=300')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('bill:mail-usages')
    ->dailyAt('08:00')
    ->withoutOverlapping();
