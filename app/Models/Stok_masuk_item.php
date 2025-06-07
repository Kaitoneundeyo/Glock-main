<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok_masuk_item extends Model
{
    protected $fillable = ['stok_masuk_id', 'produk_id', 'jumlah', 'harga_beli', 'expired_at'];

    public function stokMasuk()
    {
        return $this->belongsTo(Stok_masuk::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}

