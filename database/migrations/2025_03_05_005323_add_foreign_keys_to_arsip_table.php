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
        Schema::table('arsip', function (Blueprint $table) {
            $table->foreign(['document_id'], 'fk_arsip_memo1')->references(['id_memo'])->on('memo')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['user_id'], 'fk_arsip_users1')->references(['id'])->on('users')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            $table->dropForeign('fk_arsip_memo1');
            $table->dropForeign('fk_arsip_users1');
        });
    }
};
