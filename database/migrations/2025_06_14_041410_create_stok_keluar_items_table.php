<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokKeluarItemsTable extends Migration
{
    public function up()
    {
        Schema::create('stok_keluar_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_keluar_id')->constrained('stok_keluars')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('restrict');
            $table->integer('jumlah'); // jumlah produk yang dikeluarkan
            $table->decimal('harga_satuan', 10, 2);
            $table->decimal('total', 12, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stok_keluar_items');
    }
}
