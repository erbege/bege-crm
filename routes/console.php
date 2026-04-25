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
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/recurring-invoices.log'));

// Check for overdue invoices daily at 00:00
Schedule::command('invoices:check-overdue')
    ->daily()
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/overdue-invoices.log'));

// Send payment reminders daily at 08:00 AM
Schedule::command('invoices:send-reminders')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer();

// Update Hotspot Voucher Status (Every Minute)
Schedule::command('hotspot:update-status')
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

// Clean Stale Hotspot Sessions (Idle for 15+ mins)
Schedule::command('hotspot:clean-stale-sessions')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

// Monitor NAS Device Connectivity (Every 5 Minutes)
Schedule::command('nas:check-status')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

// Daily Log & Model Cleanup
Schedule::command('activitylog:clean')->daily()->onOneServer();
Schedule::command('model:prune')->daily()->onOneServer();
