<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('risalah', function (Blueprint $table) {
            $table->id('id_risalah');
            $table->timestamp('tgl_dibuat')->nullable();
            $table->timestamp('tgl_disahkan')->nullable();
            $table->integer('seri_surat')->nullable();
            $table->string('nomor_risalah', 255);
            $table->string('tujuan', 255)->nullable();
            $table->string('waktu_mulai', 50)->nullable();
            $table->string('waktu_selesai', 50)->nullable();
            $table->string('agenda', 255)->nullable();
            $table->string('tempat', 255)->nullable();
            $table->string('nama_bertandatangan', 255)->nullable();
            $table->longText('lampiran')->nullable();
            $table->string('judul', 255)->nullable();
            $table->string('pembuat', 45)->nullable();
            $table->longText('catatan')->nullable();
            $table->unsignedBigInteger('divisi_id_divisi')->nullable(); // Optional relasi
            $table->enum('status', ['approve', 'pending', 'reject'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risalah');
    }
};