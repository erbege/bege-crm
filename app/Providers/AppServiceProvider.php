<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;
use App\Models\Nas;
use App\Observers\SubscriptionObserver;
use App\Observers\NasObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || str_contains(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Model::preventLazyLoading(!app()->isProduction());

        Subscription::observe(SubscriptionObserver::class);
        \App\Models\BwProfile::observe(\App\Observers\BwProfileObserver::class);
        \App\Models\Invoice::observe(\App\Observers\InvoiceObserver::class);
        Nas::observe(NasObserver::class);
        \App\Models\HotspotProfile::observe(\App\Observers\HotspotProfileObserver::class);
    }
}
