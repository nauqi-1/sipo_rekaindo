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
            $table->integer('seri_bulanan');
            $table->integer('seri_tahunan');
            $table->string('kd_instansi', 25);
            $table->string('kd_internal', 25);
            $table->string('kd_bulan', 5);
            $table->date('tahun');
            $table->integer('id_divisi')->index('fk_divisi');
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
