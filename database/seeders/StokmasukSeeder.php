<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stok_masuk;

class StokmasukSeeder extends Seeder
{
    public function run()
    {
        // Gunakan supplier_id yang sudah ada
        Stok_masuk::create([
            'no_invoice' => 'RYM-00001/2025/05/30',
            'tanggal_masuk' =>  now(),
            'supplier_id' => 1, // supplier pertama
        ]);

        Stok_masuk::create([
            'no_invoice' => 'RYM-00002/2025/05/30',
            'tanggal_masuk' =>  now(),
            'supplier_id' => 2, // supplier kedua
        ]);
    }
}
