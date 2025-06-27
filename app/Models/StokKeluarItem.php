<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StokKeluarItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'stok_keluars_id',
        'produk_id',
        'jumlah',
        'harga_beli',
        'harga_jual',
    ];

    public function stokKeluar()
    {
        return $this->belongsTo(StokKeluar::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
