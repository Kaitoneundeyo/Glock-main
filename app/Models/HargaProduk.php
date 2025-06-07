<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HargaProduk extends Model
{
    use HasFactory;

    protected $table = 'harga_produk';

    protected $fillable = [
        'produk_id',
        'harga_jual',
        'harga_promo',
        'tanggal_mulai_promo',
        'tanggal_selesai_promo',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
