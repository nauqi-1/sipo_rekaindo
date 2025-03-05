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
        Schema::create('memo', function (Blueprint $table) {
            $table->integer('id_memo', true);
            $table->string('judul');
            $table->string('tujuan');
            $table->longText('isi_memo');
            $table->date('tgl_dibuat');
            $table->date('tgl_disahkan')->nullable();
            $table->enum('status', ['pending', 'approve', 'reject'])->default('pending');
            $table->string('nomor_memo');
            $table->string('nama_bertandatangan');
            $table->binary('tanda_identitas')->nullable();
            $table->timestamps();
            $table->integer('divisi_id_divisi')->index('fk_memo_divisi1_idx');
            $table->integer('seri_surat');
            $table->string('pembuat', 45);
            $table->longText('catatan')->nullable();

            $table->primary(['id_memo', 'divisi_id_divisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memo');
    }
};
