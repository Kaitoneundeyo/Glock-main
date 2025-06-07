<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'nama_supplier' => 'PT Sumber Pangan Abadi',
                'alamat' => 'Jl. Raya Industri No. 12, Jakarta',
                'kontak' => '081234567890',
            ],
            [
                'nama_supplier' => 'CV Frozen Food Mandiri',
                'alamat' => 'Jl. Ahmad Yani No. 8, Bandung',
                'kontak' => '082345678901',
            ],
            [
                'nama_supplier' => 'Toko Daging Segar',
                'alamat' => 'Jl. Soekarno Hatta No. 23, Surabaya',
                'kontak' => '083456789012',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}

