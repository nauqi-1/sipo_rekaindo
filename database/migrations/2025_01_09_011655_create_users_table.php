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
        Schema::create('users', function (Blueprint $table) {
            $table->integer('id_user', true);
            $table->string('firrst_name', 50);
            $table->string('last_name', 50);
            $table->string('username', 25);
            $table->string('password', 15);
            $table->string('email', 70);
            $table->integer('phone_number');
            $table->binary('image')->nullable();
            $table->integer('id_role')->nullable()->index('fk_user3');
            $table->integer('id_divisi')->index('fk_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
