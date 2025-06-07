<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['nama_supplier', 'alamat', 'kontak'];

    public function stokMasuks()
{
    return $this->hasMany(Stok_masuk::class);
}

}
