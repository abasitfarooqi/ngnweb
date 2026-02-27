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
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->foreign(['country_id'])->references(['id'])->on('system_countries')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['user_id'])->references(['id'])->on('users-old')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropForeign('user_addresses_country_id_foreign');
            $table->dropForeign('user_addresses_user_id_foreign');
        });
    }
};
