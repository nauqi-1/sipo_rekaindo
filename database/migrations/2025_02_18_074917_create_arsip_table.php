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
        Schema::create('arsip', function (Blueprint $table) {
            $table->integer('id_arsip', true);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('memo_id');
            $table->timestamps();

            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('memo_id')->references('id')->on('memo')->onDelete('cascade');

            // Supaya tidak ada duplikasi arsip untuk user yang sama
            $table->unique(['user_id', 'memo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */ 
    public function down(): void
    {
        Schema::dropIfExists('arsip');
    }
};
