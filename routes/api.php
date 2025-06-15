<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\MidtransWebhookController;

// ✅ Route untuk menerima notifikasi dari server Midtrans (webhook)
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'notification'])
    ->name('midtrans.webhook');

// ⚠️ Opsional: Jika kamu ingin menerima callback (postback) dari Midtrans secara server-side
// (biasanya sudah ditangani via webhook, jadi ini jarang diperlukan)
Route::post('/midtrans/callback', [MidtransController::class, 'finish']);
