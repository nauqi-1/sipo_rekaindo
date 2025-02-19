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
        Schema::create('risalah', function (Blueprint $table) {
            $table->integer('id_risalah', true);
            $table->string('judul');
            $table->string('tujuan');
            $table->longText('isi_risalah');
            $table->timestamp('tgl_dibuat')->useCurrent();
            $table->timestamp('tgl_disahkan')->nullable();
            $table->enum('status', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('nomor_risalah');
            $table->string('nama_bertandatangan');
            $table->integer('divisi_id_divisi')->index('fk_document_divisi1_idx');
            $table->binary('tanda_identitas')->nullable();
            $table->integer('seri_surat');
            $table->timestamps();
            $table->string('pembuat');

            $table->primary(['id_risalah', 'divisi_id_divisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risalah');
    }
};
