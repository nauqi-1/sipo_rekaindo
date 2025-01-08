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
        Schema::table('risalah', function (Blueprint $table) {
            $table->foreign(['id_divisi'], 'fk_risalah')->references(['id_divisi'])->on('divisi')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_document'], 'fk_risalah1')->references(['id_document'])->on('document')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risalah', function (Blueprint $table) {
            $table->dropForeign('fk_risalah');
            $table->dropForeign('fk_risalah1');
        });
    }
};
