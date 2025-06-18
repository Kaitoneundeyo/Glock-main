<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CobaController;
use App\Http\Controllers\GambarProdukController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KonfirmasiController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\HargaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\StokmasukController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\StokkeluarController;
use App\Http\Controllers\StokKeluarItemController;
use App\Http\Controllers\TampilanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UtamaController;

// ========================================
// PUBLIC ROUTES (Tanpa middleware auth)
// ========================================

// Auth
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::get('/login-google', [AuthController::class, 'indexGoogle'])->name('login-google');
Route::post('/login-proses', [AuthController::class, 'login_proses'])->name('login-proses');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Halaman utama
Route::get('/home', [HomeController::class, 'index'])->name('home.index');

// ========================================
// MIDTRANS CALLBACK & WEBHOOK ROUTES
// ========================================

// Webhook / Notification (tanpa auth)
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'notification'])->name('midtrans.webhook');
Route::post('/midtrans/notification', [MidtransWebhookController::class, 'notification'])->name('midtrans.notification');

// Midtrans redirect dari browser user (tanpa auth)
Route::get('/checkout/finish', [CheckoutController::class, 'finish'])->name('checkout.finish');
Route::get('/checkout/unfinish', [CheckoutController::class, 'unfinish'])->name('checkout.unfinish');
Route::get('/checkout/error', [CheckoutController::class, 'error'])->name('checkout.error');

// ========================================
// AUTHENTICATED ROUTES
// ========================================
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [TampilanController::class, 'index'])->name('tampil.index');

    // User Management
    Route::get('/user', [UtamaController::class, 'index'])->name('user.index');
    Route::get('/user/create', [UtamaController::class, 'create'])->name('user.create');
    Route::post('/user/store', [UtamaController::class, 'store'])->name('user.store');
    Route::get('/user/{id}/edit', [UtamaController::class, 'edit'])->name('user.edit');
    Route::put('/user/{id}/update', [UtamaController::class, 'update'])->name('user.update');
    Route::delete('/user/{id}', [UtamaController::class, 'destroy'])->name('user.destroy');

    // Kategori
    Route::get('/kt', [CategoriesController::class, 'index'])->name('kategori.index');
    Route::get('/kt/create', [CategoriesController::class, 'create'])->name('kategori.create');
    Route::post('/kt/store', [CategoriesController::class, 'store'])->name('kategori.store');
    Route::get('/kt/{id}/edit', [CategoriesController::class, 'edit'])->name('kategori.edit');
    Route::put('/kt/{id}/update', [CategoriesController::class, 'update'])->name('kategori.update');
    Route::delete('/kt/{id}/destroy', [CategoriesController::class, 'destroy'])->name('kategori.destroy');

    // Produk & Inventori
    Route::get('/pd', [ProdukController::class, 'index'])->name('produk.index');
    Route::get('/sp', [SupplierController::class, 'index'])->name('supplier.index');
    Route::get('/iv', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/st', [StokmasukController::class, 'index'])->name('stokmasuk.index');
    Route::get('/stokmasuk/export/today', [StokmasukController::class, 'exportToday'])->name('stokmasuk.export.today');

    // Item Detail
    Route::get('/it/{id}', function ($id) {
        return view('item.index', ['id' => $id]);
    })->name('item.index');

    // Gambar & Harga
    Route::get('/gb', [GambarProdukController::class, 'index'])->name('gambar.index');
    Route::get('/hg', [HargaController::class, 'index'])->name('harga.index');

    // Testing & Konfirmasi
    Route::get('/cb', [CobaController::class, 'index'])->name('coba.index');
    Route::get('/by', [KonfirmasiController::class, 'index'])->name('konfir.index');

    // ========================================
    // CHECKOUT & MIDTRANS PAYMENT
    // ========================================

    // Checkout

    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
    Route::get('/checkout/transactions', [CheckoutController::class, 'transactions'])->name('checkout.transactions');
    Route::delete('/checkout/cancel/{order}', [CheckoutController::class, 'cancelOrder'])->name('checkout.cancel');


    // Midtrans Payment
    Route::get('/midtrans/pay/{order}', [MidtransController::class, 'pay'])->name('midtrans.pay');
    Route::post('/midtrans/create-transaction/{orderNumber}', [MidtransController::class, 'createTransaction'])->name('midtrans.create-transaction');

    // Optional manual test routes
    Route::get('/midtrans/finish', [MidtransController::class, 'finish'])->name('midtrans.finish');
    Route::get('/midtrans/unfinish', [MidtransController::class, 'unfinish'])->name('midtrans.unfinish');
    Route::get('/midtrans/error', [MidtransController::class, 'error'])->name('midtrans.error');

    Route::get('/stk', [StokKeluarController::class, 'index'])->name('stokkeluar.index');
    Route::get('/stki/{id}', [StokKeluarItemController::class, 'index'])->name('itemkeluar.index');
});
