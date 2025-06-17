<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\MidtransWebhookController;

// ✅ Route untuk menerima notifikasi dari server Midtrans (webhook)
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'notification'])
    ->name('midtrans.webhook');

Route::post('/midtrans/callback', [MidtransController::class, 'callback'])->name('midtrans.callback');
