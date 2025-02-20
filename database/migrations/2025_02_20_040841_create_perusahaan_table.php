<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instansi');
            $table->string('alamat_web');
            $table->string('telepon');
            $table->string('email');
            $table->text('alamat');
            $table->string('logo')->nullable(); // Untuk menyimpan nama file logo
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('perusahaan');
    }
};