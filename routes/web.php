<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CobaController;
use App\Http\Controllers\GambarProdukController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\KonfirmasiController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\HargaController;
use App\Http\Controllers\HistoriController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StokmasukController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TampilanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UtamaController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::get('/login-google', [AuthController::class, 'indexGoogle'])->name('login-google');
Route::post('/login-proses',  [AuthController::class, 'login_proses'])->name('login-proses');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/home', [HomeController::class, 'index'])->name('home.index');
Route::get('/dashboard', [TampilanController::class, 'index'])->name('tampil.index');

Route::get('/user', [UtamaController::class, 'index'])->name('user.index');
Route::get('/user/create', [UtamaController::class, 'create'])->name('user.create');
Route::post('/user/store', [UtamaController::class, 'store'])->name('user.store');
Route::get('/user/{id}/edit', [UtamaController::class, 'edit'])->name('user.edit');
Route::put('/user/{id}/update', [UtamaController::class, 'update'])->name('user.update');
Route::delete('/user/{id}', [UtamaController::class, 'destroy'])->name('user.destroy');


Route::get('/kt', [CategoriesController::class, 'index'])->name('kategori.index');
Route::get('/kt/create', [CategoriesController::class, 'create'])->name('kategori.create');
Route::post('/kt/store', [CategoriesController::class, 'store'])->name('kategori.store');
Route::get('/kt/{id}/edit', [CategoriesController::class, 'edit'])->name('kategori.edit');
Route::put('/kt/{id}/update', [CategoriesController::class, 'update'])->name('kategori.update');
Route::delete('/kt/{id}/destroy', [CategoriesController::class, 'destroy'])->name('kategori.destroy');


Route::get('/pd', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/sp', [SupplierController::class, 'index'])->name('supplier.index');
Route::get('/iv', [InvoiceController::class, 'index'])->name('invoice.index');

Route::get('/st', [StokmasukController::class, 'index'])->name('stokmasuk.index');
Route::get('/stokmasuk/export/today', [StokmasukController::class, 'exportToday'])->name('stokmasuk.export.today');
Route::get('/it/{id}', function ($id) {
    return view('item.index', ['id' => $id]);
})->name('item.index');

Route::get('/gb', [GambarProdukController::class, 'index'])->name('gambar.index');
Route::get('/hg', [HargaController::class, 'index'])->name('harga.index');
Route::get('/cb', [CobaController::class, 'index'])->name('coba.index');
Route::get('/by', [KonfirmasiController::class, 'index'])->name('konfir.index');
Route::get('/bk', [HistoriController::class, 'index'])->name('bukti.index');

Route::middleware(['auth'])->group(function () {
    Route::get('/checkout/transactions', [CheckoutController::class, 'index'])->name('checkout.transactions');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/confirmation/{invoice}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
});
