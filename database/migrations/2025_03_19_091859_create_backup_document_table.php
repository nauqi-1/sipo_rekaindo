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
        Schema::create('backup_document', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('id_document');
            $table->string('jenis_document');
            $table->string('judul');
            $table->string('tujuan');
            $table->longText('isi_document');
            $table->longText('catatan');
            $table->integer('seri_document');
            $table->date('tgl_dibuat');
            $table->date('tgl_disahkan')->nullable();
            $table->enum('status', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('nomor_document');
            $table->string('nama_bertandatangan');
            $table->integer('divisi_id_divisi')->index('fk_document_divisi1_idx');
            $table->longText('lampiran')->nullable();
            $table->timestamps();

            $table->primary(['id', 'divisi_id_divisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_document');
    }
};
