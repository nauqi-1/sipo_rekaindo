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
        Schema::create('document', function (Blueprint $table) {
            $table->integer('id_document', true);
            $table->string('jenis_document');
            $table->string('judul');
            $table->string('tujuan');
            $table->longText('isi_document');
            $table->integer('seri_bulanan');
            $table->integer('seri_tahunan');
            $table->timestamp('tgl_dibuat')->useCurrent();
            $table->timestamp('tgl_disahkan')->nullable();
            $table->integer('bulan');
            $table->integer('tahun');
            $table->enum('status', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('nomor_document');
            $table->string('nama_pimpinan');
            $table->integer('divisi_id_divisi')->index('fk_document_divisi1_idx');
            $table->binary('tanda_identitas')->nullable();
            $table->timestamps();

            $table->primary(['id_document', 'divisi_id_divisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document');
    }
};
