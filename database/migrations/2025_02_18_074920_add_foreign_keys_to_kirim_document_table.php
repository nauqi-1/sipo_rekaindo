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
        Schema::table('kirim_document', function (Blueprint $table) {
            $table->foreign(['id_document'], 'fk_memo_kirim')->references(['id_memo'])->on('memo')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kirim_document', function (Blueprint $table) {
            $table->dropForeign('fk_memo_kirim');
        });
    }
};
