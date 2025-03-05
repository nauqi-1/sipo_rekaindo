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
            $table->foreign(['divisi_id_divisi'], 'fk_users_divisi1')->references(['id_divisi'])->on('divisi')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['position_id_position'], 'fk_users_position1')->references(['id_position'])->on('position')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['role_id_role'], 'fk_users_role')->references(['id_role'])->on('role')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('fk_users_divisi1');
            $table->dropForeign('fk_users_position1');
            $table->dropForeign('fk_users_role');
        });
    }
};
