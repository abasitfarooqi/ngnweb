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
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->foreign(['inventory_id'])->references(['id'])->on('inventories')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users-old')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_histories', function (Blueprint $table) {
            $table->dropForeign('inventory_histories_inventory_id_foreign');
            $table->dropForeign('inventory_histories_user_id_foreign');
        });
    }
};
