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
            $table->integer('id_notifikasi')->primary();
            $table->string('judul', 100);
            $table->string('jenis_document', 60);
            $table->integer('id_divisi')->index('notif-1_idx');
            $table->timestamp('updated_at')->nullable();
            $table->boolean('dibaca')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifikasi', function (Blueprint $table) {
            $table->dropColumn('dibaca');
        });
    }
};
