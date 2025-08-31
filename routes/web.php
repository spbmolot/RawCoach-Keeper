<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\YooKassaWebhookController;
use App\Http\Controllers\Webhook\CloudPaymentsWebhookController;

Route::get('/', function () {
    return view('welcome');
});

// Payment provider webhooks
Route::post('/webhook/yookassa', YooKassaWebhookController::class);
Route::post('/webhook/cloudpayments', CloudPaymentsWebhookController::class);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
