<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok_masuk extends Model
{
    protected $fillable = ['no_invoice', 'tanggal_masuk', 'supplier_id'];

    public function items()
    {
        return $this->hasMany(Stok_masuk_item::class);
    }

    public function supplier()
{
    return $this->belongsTo(Supplier::class);
}

}

