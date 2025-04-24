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
        Schema::create('backup_risalah', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_document');
            $table->string('jenis_document');
            $table->timestamp('tgl_dibuat');
            $table->timestamp('tgl_disahkan')->nullable();
            $table->integer('seri_document');
            $table->string('nomor_document');
            $table->string('tujuan');
            $table->string('waktu_mulai');
            $table->string('waktu_selesai');
            $table->string('agenda');
            $table->string('tempat');
            $table->string('nama_bertandatangan');
            $table->longText('lampiran')->nullable();
            $table->string('judul');
            $table->longText('catatan');
            $table->integer('divisi_id_divisi')->index('fk_document_divisi1_idx');
            $table->enum('status', ['pending', 'approve', 'reject'])->default('pending');
            $table->timestamps();

            $table->primary(['id', 'divisi_id_divisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_risalah');
    }
};
