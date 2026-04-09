<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\Api\CustomerApiController;

Route::prefix('v1/customer')->name('api.customer.')->group(function () {
    Route::post('/login', [CustomerApiController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [CustomerApiController::class, 'logout']);

        // Subscription & Stats
        Route::get('/dashboard', [\App\Http\Controllers\Api\CustomerApiController::class, 'dashboard']);
    });
});
