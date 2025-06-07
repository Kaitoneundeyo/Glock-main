<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart_item extends Model
{
    use HasFactory;

    protected $table = 'cart_items';
    protected $fillable = [
        'user_id',
        'produk_id',
        'quantity',
    ];
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
