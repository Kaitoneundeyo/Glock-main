<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stok_masuks', function (Blueprint $table) {
            // Ubah kolom menjadi datetime
            $table->dateTime('tanggal_masuk')->change();
        });
    }

    public function down(): void
    {
        Schema::table('stok_masuks', function (Blueprint $table) {
            // Balikkan jadi date
            $table->date('tanggal_masuk')->change();
        });
    }
};
