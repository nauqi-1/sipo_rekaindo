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
            $table->integer('id_kategori_barang', true);
            $table->integer('nomor');
            $table->string('barang', 100);
            $table->integer('qty');
            $table->string('satuan', 50);
            $table->timestamps();
            $table->integer('memo_id_memo');
            $table->integer('memo_divisi_id_divisi');

            $table->index(['memo_id_memo', 'memo_divisi_id_divisi'], 'fk_kategori_barang_memo1_idx');
            $table->primary(['id_kategori_barang', 'memo_id_memo', 'memo_divisi_id_divisi']);
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
