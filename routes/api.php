<?php

use App\Http\Controllers\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\MidtransWebhookController;

Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle']);
Route::post('/midtrans/callback', [MidtransController::class, 'callback']);
Route::get('/checkout/finish', [CheckoutController::class, 'successFromFrontend'])->name('checkout.finish');
