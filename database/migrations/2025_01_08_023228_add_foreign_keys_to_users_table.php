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
        Schema::table('users', function (Blueprint $table) {
            $table->foreign(['id_divisi'], 'fk_user')->references(['id_divisi'])->on('divisi')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_position'], 'fk_user1')->references(['id_position'])->on('position')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('fk_user');
            $table->dropForeign('fk_user1');
        });
    }
};
