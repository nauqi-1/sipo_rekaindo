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
            $table->string('nm_risalah', 70);
            $table->date('tgl_dibuat');
            $table->date('tgl_disahkan')->nullable();
            $table->binary('lampiran');
            $table->integer('id_divisi')->index('fk_risalah');
            $table->integer('id_document')->index('fk_risalah1');
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
