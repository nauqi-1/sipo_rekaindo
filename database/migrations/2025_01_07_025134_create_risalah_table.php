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
            $table->integer('id_risalah');
            $table->string('nm_risalah', 70);
            $table->date('tgl_dibuat');
            $table->date('tgl_disahkan')->nullable();
            $table->string('dokumen', 50);
            $table->binary('lampiran');
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
