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
        Schema::create('user', function (Blueprint $table) {
            $table->integer('id_user', true);
            $table->string('firrst_name', 50);
            $table->string('last_name', 50);
            $table->string('username', 25);
            $table->string('password', 15);
            $table->string('email', 70);
            $table->integer('phone_number');
            $table->binary('image')->nullable();
            $table->string('role', 15)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
