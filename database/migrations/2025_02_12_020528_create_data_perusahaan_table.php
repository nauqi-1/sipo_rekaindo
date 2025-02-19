<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_perusahaan', function (Blueprint $table) {
            $table->integer('id_prshn', true);
            $table->string('nm_prshn');
            $table->string('alamat_web');
            $table->string('telepon', 15);
            $table->string('email');
            $table->text('alamat');
            $table->string('logo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_perusahaan');
    }
};
