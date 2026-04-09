<?php

use App\Livewire\MasterData\NasManager;
use App\Livewire\MasterData\BwProfileManager;
use App\Livewire\MasterData\PackageManager;
use App\Livewire\Coverage\AreaManager;
use App\Livewire\Coverage\CoveragePointManager;

use App\Livewire\Coverage\CoverageMap;
use App\Livewire\MasterData\OltManager;
use App\Livewire\MasterData\ScriptTemplateManager;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

    // Master Data Routes
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/nas', NasManager::class)->name('nas');

        Route::get('/bw-profiles', BwProfileManager::class)->name('bw-profiles');
        Route::get('/packages', PackageManager::class)->name('packages');
        Route::get('/olts', OltManager::class)->name('olts');
        Route::get('/script-templates', ScriptTemplateManager::class)->name('script-templates');
    });

    // Coverage Routes
    Route::prefix('coverage')->name('coverage.')->group(function () {
        Route::get('/areas', AreaManager::class)->name('areas');
        Route::get('/points', CoveragePointManager::class)->name('points');
        Route::get('/map', CoverageMap::class)->name('map');
    });

    // Customer Routes
    Route::get('/customers', \App\Livewire\Customer\CustomerManager::class)->name('customers.index');

    // Subscription Routes
    Route::get('/subscriptions', \App\Livewire\Subscription\SubscriptionManager::class)->name('subscriptions.index');
    Route::get('/subscriptions/online', \App\Livewire\Subscription\OnlineUsers::class)->name('subscriptions.online');

    // Invoice Routes
    Route::get('/invoices', \App\Livewire\Invoice\InvoiceManager::class)->name('invoices.index');

    // Settings Routes
    Route::get('/settings', \App\Livewire\Settings\SettingsManager::class)->name('settings.index');

    // Provisioning Routes
    Route::get('/provisioning/{subscription}/script', [\App\Http\Controllers\ProvisioningController::class, 'showScript'])->name('provisioning.script');
    Route::post('/provisioning/{subscription}/push', [\App\Http\Controllers\ProvisioningController::class, 'pushToOlt'])->name('provisioning.push');

    // Monitoring Route
    Route::get('/network/check/{customer}', [\App\Http\Controllers\NetworkCheckController::class, 'checkStatus'])->name('network.check');
    // Hotspot Management
    Route::get('/hotspot/profiles', App\Livewire\Hotspot\ProfileManager::class)->name('hotspot.profiles');
    Route::get('/hotspot/vouchers', App\Livewire\Hotspot\VoucherList::class)->name('hotspot.vouchers');
    Route::get('/hotspot/generator', App\Livewire\Hotspot\VoucherGenerator::class)->name('hotspot.generate');
    Route::get('/hotspot/active-sessions', App\Livewire\Hotspot\ActiveSessions::class)->name('hotspot.active-sessions');
    Route::get('/hotspot/templates', \App\Livewire\Hotspot\TemplateManager::class)->name('hotspot.templates');
    Route::get('/hotspot/vouchers/{batch}/print', [\App\Http\Controllers\HotspotVoucherController::class, 'print'])->name('hotspot.vouchers.print');

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/financial', \App\Livewire\Reports\FinancialReport::class)->name('financial');
        Route::get('/customers', \App\Livewire\Reports\CustomerReport::class)->name('customers');
    });

    // Notifications
    Route::get('/notifications', \App\Livewire\Notification\WhatsappNotificationManager::class)->name('notifications.index');

    // Tickets
    Route::get('/tickets', \App\Livewire\Ticket\TicketManager::class)->name('tickets.index');

    // Administration Routes
    Route::middleware(['role:super-admin|admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', \App\Livewire\Admin\UserManager::class)->name('users');
    });

    Route::middleware(['role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/roles', \App\Livewire\Admin\RoleManager::class)->name('roles');
    });
});

// Public Hotspot Portal & Payment
Route::get('/hotspot/portal', [\App\Http\Controllers\HotspotPortalController::class, 'index'])->name('hotspot.portal');
Route::post('/hotspot/portal/checkout', [\App\Http\Controllers\HotspotPortalController::class, 'checkout'])
    ->name('hotspot.checkout')
    ->middleware('throttle:5,1');

Route::get('/hotspot/waiting/{reference}', [\App\Http\Controllers\HotspotPortalController::class, 'waiting'])->name('hotspot.waiting');
Route::get('/hotspot/status/{reference}', [\App\Http\Controllers\HotspotPortalController::class, 'status'])
    ->name('hotspot.status')
    ->middleware('throttle:60,1');

Route::get('/hotspot/success/{reference}', [\App\Http\Controllers\HotspotPortalController::class, 'success'])->name('hotspot.success');
Route::get('/hotspot/print/{reference}', [\App\Http\Controllers\HotspotPortalController::class, 'print'])->name('hotspot.print');

require __DIR__ . '/portal.php';
