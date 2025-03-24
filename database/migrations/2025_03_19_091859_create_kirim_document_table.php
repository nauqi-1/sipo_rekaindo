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
        Schema::create('kirim_document', function (Blueprint $table) {
            $table->integer('id_kirim_document', true);
            $table->integer('id_document');
            $table->string('jenis_document', 45);
            $table->bigInteger('id_pengirim');
            $table->bigInteger('id_penerima');
            $table->string('status', 45);
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kirim_document');
    }
};
