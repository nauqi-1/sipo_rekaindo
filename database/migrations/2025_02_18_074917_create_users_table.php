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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname', 50);
            $table->string('lastname', 50);
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->bigInteger('phone_number');
            $table->rememberToken();
            $table->timestamps();
            $table->integer('role_id_role')->index('fk_users_role_idx');
            $table->integer('position_id_position')->index('fk_users_position1_idx');
            $table->integer('divisi_id_divisi')->index('fk_users_divisi1_idx');

            $table->primary(['id', 'role_id_role', 'position_id_position', 'divisi_id_divisi']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
