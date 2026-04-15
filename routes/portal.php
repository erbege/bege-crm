<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Portal\CustomerAuthController;

Route::prefix('portal')->name('portal.')->group(function () {
    // Auth Routes
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login')->middleware('guest:customer');
    Route::post('/login', [CustomerAuthController::class, 'login'])->middleware('guest:customer');

    Route::middleware('auth:customer')->group(function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

        // Customer Dashboard
        Route::get('/dashboard', \App\Livewire\Portal\Dashboard::class)->name('dashboard');
        Route::get('/invoices', \App\Livewire\Portal\InvoiceManager::class)->name('invoices');
        Route::get('/tickets', \App\Livewire\Portal\TicketManager::class)->name('tickets');

        // Payment
        Route::get('/invoices/{invoice}/pay', [\App\Http\Controllers\Portal\PaymentController::class, 'checkout'])->name('invoices.pay');
    });

    // Webhooks
    Route::post('/webhooks/payment', [\App\Http\Controllers\Portal\PaymentController::class, 'webhook'])->name('webhooks.payment');
});
