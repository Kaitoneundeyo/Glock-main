<?php

namespace Database\Seeders;

use Database\Seeders\ProdukSeeder as SeedersProdukSeeder;
use Illuminate\Database\Seeder;
use ProdukSeeder;
use StockSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan semua seeder aplikasi.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategoriesSeeder::class,
            ProductSeeder::class,
            SupplierSeeder::class,
            StokmasukSeeder::class,
            StokmasukitemSeeder::class,
            HargaSeeder::class,
            GambarProdukSeeder::class,
        ]);
    }
}
