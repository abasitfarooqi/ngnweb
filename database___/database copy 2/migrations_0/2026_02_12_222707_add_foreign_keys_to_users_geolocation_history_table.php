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
        Schema::table('users_geolocation_history', function (Blueprint $table) {
            $table->foreign(['order_id'])->references(['id'])->on('orders')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['user_id'])->references(['id'])->on('users-old')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_geolocation_history', function (Blueprint $table) {
            $table->dropForeign('users_geolocation_history_order_id_foreign');
            $table->dropForeign('users_geolocation_history_user_id_foreign');
        });
    }
};
