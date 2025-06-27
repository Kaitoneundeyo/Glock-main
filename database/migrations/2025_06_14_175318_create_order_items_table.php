<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('price', 15, 2); // Price at time of order
            $table->decimal('subtotal', 15, 2);
            $table->decimal('hpp', 12, 2);
            $table->timestamps();

            // Indexes
            $table->index(['order_id', 'produk_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
