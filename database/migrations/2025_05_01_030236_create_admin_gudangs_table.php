<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminGudangsTable extends Migration
{
    public function up()
    {
        Schema::create('admin_gudang', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->string('warehouse_code', 50);
            $table->string('employee_number', 50);
            $table->timestamps();

            $table->primary('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_gudang');
    }
}
