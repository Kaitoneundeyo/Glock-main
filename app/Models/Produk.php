<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'merk',
        'tipe',
        'berat',
        'categories_id',
        'stok',
    ];

    // 🔁 Kategori Produk
    public function kategori()
    {
        return $this->belongsTo(Category::class, 'categories_id');
    }

    // 🔁 Relasi Stok Masuk (jika digunakan)
    public function stokMasukItems()
    {
        return $this->hasMany(Stok_masuk_item::class);
    }

    // 🔁 Harga Aktif (promo / tidak promo tergantung tanggal)
    public function hargaTerbaru()
    {
        return $this->hasOne(HargaProduk::class, 'produk_id')->latestOfMany();
    }
    // 🔁 Semua Harga (jika ingin ambil semua histori harga)
    public function semuaHarga()
    {
        return $this->hasMany(HargaProduk::class, 'produk_id');
    }

    // 🔁 Gambar Utama
    public function gambarUtama()
    {
        return $this->hasOne(GambarProduk::class, 'produk_id')->where('is_utama', true);
    }

    // 🔁 Semua Gambar
    public function gambar()
    {
        return $this->hasMany(GambarProduk::class, 'produk_id');
    }

    // 🔁 Item Penjualan
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'produk_id');
    }
}
