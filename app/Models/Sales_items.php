<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales_items extends Model
{
    use HasFactory;

    protected $table = 'sales_items';

    protected $fillable = [
        'sale_id',
        'produk_id',
        'jumlah',
        'harga_satuan',
        'total',
    ];

    /**
     * Relasi ke transaksi penjualan utama
     */
    public function sales()
    {
        return $this->belongsTo(Sales::class);
    }

    /**
     * Relasi ke produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
