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
            $table->string('nm_undangan', 70);
            $table->date('tgl_dibuat');
            $table->date('tgl_disahkan')->nullable();
            $table->binary('lampiran');
            $table->integer('id_divisi')->index('fk_undangan');
            $table->integer('id_document')->index('fk_undangan1');
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
