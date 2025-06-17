<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StokKeluar extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_keluar',
        'tanggal_keluar',
        'jenis',
    ];

    public function items()
    {
        return $this->hasMany(StokKeluarItem::class);
    }

}
