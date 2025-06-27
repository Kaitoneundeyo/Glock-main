<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('stok_keluar_items', function (Blueprint $table) {
            $table->decimal('hpp', 15, 2)->nullable()->after('harga_jual');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('stok_keluar_items', 'hpp')) {
            Schema::table('stok_keluar_items', function (Blueprint $table) {
                $table->dropColumn('hpp');
            });
        }
    }
};
