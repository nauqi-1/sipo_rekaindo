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
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->integer('id_notifikasi', true);
            $table->string('judul', 100);
            $table->integer('id_divisi')->index('fk_notif_idx');
            $table->timestamp('updated_at');
            $table->integer('dibaca');
            $table->string('judul_document');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
