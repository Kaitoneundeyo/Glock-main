<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // sama dengan BIGINT PRIMARY KEY AUTO_INCREMENT
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            // ENUM untuk role
            $table->enum('role', ['kepala_gudang', 'admin_gudang', 'kasir', 'pelanggan']);

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->string('avatar')->nullable();
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
