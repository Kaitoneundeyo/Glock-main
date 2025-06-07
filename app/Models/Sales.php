<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $table = 'Sales';

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'user_id',
    ];

    /**
     * Relasi ke User (kasir/admin yang menjual)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke item-item penjualan (sale_items)
     */
    public function saleItems()
    {
        return $this->hasMany(Sales_items::class);
    }
}
