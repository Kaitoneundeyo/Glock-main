<?php

namespace Database\Seeders;

use App\Models\Stok_masuk_item;
use Illuminate\Database\Seeder;

class StokmasukitemSeeder extends Seeder
{
    public function run()
    {
        Stok_masuk_item::create([
            'stok_masuk_id' => 1,
            'produk_id' => 1,
            'jumlah' => 10,
            'harga_beli' => 15000,
            'expired_at' => now()->addMonths(6),
        ]);

        Stok_masuk_item::create([
            'stok_masuk_id' => 1,
            'produk_id' => 2,
            'jumlah' => 20,
            'harga_beli' => 20000,
            'expired_at' => now()->addMonths(10),
        ]);

        Stok_masuk_item::create([
            'stok_masuk_id' => 2,
            'produk_id' => 1,
            'jumlah' => 15,
            'harga_beli' => 14000,
            'expired_at' => now()->addMonths(9),
        ]);

        Stok_masuk_item::create([
            'stok_masuk_id' => 2,
            'produk_id' => 2,
            'jumlah' => 25,
            'harga_beli' => 19500,
            'expired_at' => now()->addMonths(8),
        ]);
    }
}
