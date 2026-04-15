<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Generate recurring invoices daily at 1:00 AM
Schedule::command('invoices:generate-recurring')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/recurring-invoices.log'));

// Check for overdue invoices daily at 00:00
Schedule::command('invoices:check-overdue')
    ->daily()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/overdue-invoices.log'));

// Update Hotspot Voucher Status (Every Minute)
Schedule::command('hotspot:update-status')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Clean Stale Hotspot Sessions (Idle for 15+ mins)
Schedule::command('hotspot:clean-stale-sessions')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();
