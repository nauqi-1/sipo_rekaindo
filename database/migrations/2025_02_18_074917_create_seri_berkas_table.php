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
        Schema::create('seri_berkas', function (Blueprint $table) {
            $table->integer('id_seri', true);
            $table->integer('seri_bulanan');
            $table->integer('seri_tahunan');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->timestamps();
            $table->integer('divisi_id_divisi')->index('fk_seri_berkas_divisi1_idx');

            $table->primary(['id_seri', 'divisi_id_divisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seri_berkas');
    }
};
