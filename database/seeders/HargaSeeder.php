<?php

namespace Database\Seeders;

use App\Models\HargaProduk;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class HargaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HargaProduk::create([
            'produk_id' => 1,
            'harga_jual' => 20000,
            'harga_promo' => null,
            'tanggal_mulai_promo' => null,
            'tanggal_selesai_promo' => null,
        ]);

        HargaProduk::create([
            'produk_id' => 2,
            'harga_jual' => 30000,
            'harga_promo' => null,
            'tanggal_mulai_promo' => null,
            'tanggal_selesai_promo' => null,
        ]);

        HargaProduk::create([
            'produk_id' => 3,
            'harga_jual' => 25000,
            'harga_promo' => 20000,
            'tanggal_mulai_promo' => Carbon::now()->toDateString(),
            'tanggal_selesai_promo' => Carbon::now()->addWeek()->toDateString(),
        ]);

        HargaProduk::create([
            'produk_id' => 1,
            'harga_jual' => 22000,
            'harga_promo' => 18000,
            'tanggal_mulai_promo' => Carbon::now()->addDays(10)->toDateString(),
            'tanggal_selesai_promo' => Carbon::now()->addDays(17)->toDateString(),
        ]);
    }
}
