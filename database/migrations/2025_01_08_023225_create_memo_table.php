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
            $table->increments('id_memo');
            $table->string('nm_memo', 70);
            $table->date('tgl_buat');
            $table->date('tgl_disahkan')->nullable();
            $table->binary('lampiran');
            $table->integer('id_divisi')->index('fk_memo');
            $table->integer('id_document')->index('fk_memo1');
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
