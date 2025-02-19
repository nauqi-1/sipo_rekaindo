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
            $table->foreign(['divisi_id_divisi'], 'fk_document_divisi11')->references(['id_divisi'])->on('divisi')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risalah', function (Blueprint $table) {
            $table->dropForeign('fk_document_divisi11');
        });
    }
};
