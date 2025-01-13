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
            $table->string('firstname', 50);
            $table->string('lastname', 50);
            $table->string('username', 25);
            $table->string('password', 15);
            $table->string('email', 70);
            $table->integer('phone_number');
            $table->timestamps();
            $table->rememberToken();   
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
