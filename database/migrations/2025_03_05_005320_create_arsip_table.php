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
        Schema::create('arsip', function (Blueprint $table) {
            $table->integer('id_arsip', true);
            $table->integer('document_id')->index('fk_arsip_memo1_idx');
            $table->unsignedBigInteger('user_id')->index('fk_arsip_users1_idx');
            $table->string('jenis_document', 60);

            $table->primary(['id_arsip', 'document_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip');
    }
};
