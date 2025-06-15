<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->integer('quantity');
            $table->enum('type', ['soft', 'hard'])->default('soft'); // soft = cart, hard = checkout
            $table->timestamp('expires_at'); // kapan reservasi expire
            $table->string('session_id')->nullable(); // untuk track session
            $table->timestamps();

            // Index untuk performa
            $table->index(['produk_id', 'type', 'expires_at']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_reservations');
    }
};
