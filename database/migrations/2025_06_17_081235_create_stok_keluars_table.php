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
        Schema::create('stok_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('no_keluar')->unique();
            $table->dateTime('tanggal_keluar')->default(now());
            $table->enum('jenis', ['expired', 'rusak']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_keluars');
    }
};
