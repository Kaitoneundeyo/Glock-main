<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokKeluarsTable extends Migration
{
    public function up()
    {
        Schema::create('stok_keluars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade'); // relasi ke penjualan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // user yg memproses
            $table->string('no_transaksi')->unique(); // bisa diturunkan dari invoice_number
            $table->dateTime('tanggal_keluar');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stok_keluars');
    }
}

