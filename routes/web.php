<?php

use App\Http\Controllers\SeeOrderController;
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
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanBulananController;

// ========================================
// PUBLIC ROUTES (Tanpa middleware auth)
// ========================================

// Auth
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::get('/login-google', [AuthController::class, 'indexGoogle'])->name('login-google');
    Route::post('/login-proses', [AuthController::class, 'login_proses'])->name('login-proses');


    // Google OAuth
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
});



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
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    // Halaman utama
    Route::get('/home', [HomeController::class, 'index'])->name('home.index');
    // Dashboard
    Route::get('/dashboard', [TampilanController::class, 'index'])->name('tampil.index');

    // User Management
    Route::middleware(['isKepalaGudang'])->group(function () {
        Route::get('/user', [UtamaController::class, 'index'])->name('user.index');
        Route::get('/user/create', [UtamaController::class, 'create'])->name('user.create');
        Route::post('/user/store', [UtamaController::class, 'store'])->name('user.store');
        Route::get('/user/{id}/edit', [UtamaController::class, 'edit'])->name('user.edit');
        Route::put('/user/{id}/update', [UtamaController::class, 'update'])->name('user.update');
        Route::delete('/user/{id}/destroy', [UtamaController::class, 'destroy'])->name('user.destroy');

        // Gambar & Harga

        Route::get('/hg', [HargaController::class, 'index'])->name('harga.index');

        Route::get('/sp', [SupplierController::class, 'index'])->name('supplier.index');
        Route::get('/iv', [InvoiceController::class, 'index'])->name('invoice.index');
    });

    // Kategori
    Route::middleware(['isAdminGudang'])->group(function () {
        Route::get('/kt', [CategoriesController::class, 'index'])->name('kategori.index');
        Route::get('/kt/create', [CategoriesController::class, 'create'])->name('kategori.create');
        Route::post('/kt/store', [CategoriesController::class, 'store'])->name('kategori.store');
        Route::get('/kt/{id}/edit', [CategoriesController::class, 'edit'])->name('kategori.edit');
        Route::put('/kt/{id}/update', [CategoriesController::class, 'update'])->name('kategori.update');
        Route::delete('/kt/{id}/destroy', [CategoriesController::class, 'destroy'])->name('kategori.destroy');
        // Produk & Inventori
        Route::get('/gb', [GambarProdukController::class, 'index'])->name('gambar.index');
        Route::get('/pd', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/st', [StokmasukController::class, 'index'])->name('stokmasuk.index');
        Route::get('/stokmasuk/export/today', [StokmasukController::class, 'exportToday'])->name('stokmasuk.export.today');
        // Item Detail
        Route::get('/it/{id}', function ($id) {
            return view('item.index', ['id' => $id]);
        })->name('item.index');
        Route::get('/stk', [StokKeluarController::class, 'index'])->name('stokkeluar.index');
        Route::get('/stki/{id}', [StokKeluarItemController::class, 'index'])->name('itemkeluar.index');
    });

    Route::middleware(['isKasir'])->group(function () {
        Route::get('/lp', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');
        Route::get('/laporan-bulanan', [LaporanBulananController::class, 'index'])->name('laporanbulanan.index');
        Route::get('/laporan-bulanan/export', [LaporanBulananController::class, 'exportExcel'])->name('laporanbulanan.export');
        Route::get('/ord', [SeeOrderController::class, 'index'])->name('order.index');
    });

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
});
