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
        Schema::create('undangan', function (Blueprint $table) {
            $table->integer('id_undangan', true);
            $table->string('judul');
            $table->string('tujuan');
            $table->longText('isi_undangan');
            $table->timestamp('tgl_dibuat');
            $table->timestamp('tgl_disahkan')->nullable();
            $table->enum('status', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('nomor_undangan');
            $table->string('nama_bertandatangan');
            $table->integer('divisi_id_divisi')->index('fk_document_divisi1_idx');
            $table->integer('seri_surat');
            $table->binary('tanda_identitas')->nullable();
            $table->timestamps();
            $table->string('pembuat');
            $table->string('catatan', 45)->nullable();

            $table->primary(['id_undangan', 'divisi_id_divisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('undangan');
    }
};
