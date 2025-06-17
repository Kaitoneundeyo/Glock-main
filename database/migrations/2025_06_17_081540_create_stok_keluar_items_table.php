<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stok_keluar_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_keluars_id')->constrained('stok_keluars')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->string('nama_produk'); // cache nama produk saat itu
            $table->integer('jumlah');
            $table->decimal('harga_beli', 12, 2);
            $table->decimal('harga_jual', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_keluar_items');
    }
};
