<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'produk_id',
        'quantity',
        'price'
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
