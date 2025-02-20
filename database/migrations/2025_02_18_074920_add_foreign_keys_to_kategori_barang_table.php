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
            $table->foreign(['memo_id_memo', 'memo_divisi_id_divisi'], 'fk_kategori_barang_memo1')->references(['id_memo', 'divisi_id_divisi'])->on('memo')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->dropForeign('fk_kategori_barang_memo1');
        });
    }
};
