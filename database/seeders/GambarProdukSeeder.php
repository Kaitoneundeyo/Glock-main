<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GambarProduk;

class GambarProdukSeeder extends Seeder
{
    public function run(): void
    {
        GambarProduk::insert([
            // Produk ID 1 = Sosis Keju
            [
                'produk_id' => 1,
                'path' => 'gambar_produk/sosis1.jpg',
                'is_utama' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'produk_id' => 1,
                'path' => 'gambar_produk/sosis2.jpg',
                'is_utama' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Produk ID 2 = Nugget Ayam
            [
                'produk_id' => 2,
                'path' => 'gambar_produk/nugget1.jpg',
                'is_utama' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'produk_id' => 2,
                'path' => 'gambar_produk/nugget2.jpg',
                'is_utama' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
