<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('risalah_details', function (Blueprint $table) {
            $table->id('id_risalah_detail');
            $table->unsignedBigInteger('risalah_id_risalah');
            $table->integer('nomor')->nullable();
            $table->string('topik', 255)->nullable();
            $table->longText('pembahasan')->nullable();
            $table->string('tindak_lanjut', 500)->nullable();
            $table->string('target', 100)->nullable();
            $table->string('pic', 100)->nullable();
            $table->timestamps();

            $table->foreign('risalah_id_risalah')->references('id_risalah')->on('risalah')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risalah_details');
    }
};