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
        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->foreign(['document_id_document', 'document_divisi_id_divisi'], 'fk_kategori_barang_document1')->references(['id_document', 'divisi_id_divisi'])->on('document')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->dropForeign('fk_kategori_barang_document1');
        });
    }
};
