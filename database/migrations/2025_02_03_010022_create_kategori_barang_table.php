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
        Schema::create('kategori_barang', function (Blueprint $table) {
            $table->integer('id_kategori_barang');
            $table->integer('nomor');
            $table->string('barang', 100);
            $table->integer('qty');
            $table->string('satuan', 50);
            $table->timestamps();
            $table->integer('document_id_document');
            $table->integer('document_divisi_id_divisi');

            $table->index(['document_id_document', 'document_divisi_id_divisi'], 'fk_kategori_barang_document1_idx');
            $table->primary(['id_kategori_barang', 'document_id_document', 'document_divisi_id_divisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_barang');
    }
};
