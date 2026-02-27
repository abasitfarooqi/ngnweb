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
        Schema::table('ds_order_items', function (Blueprint $table) {
            $table->foreign(['ds_order_id'])->references(['id'])->on('ds_orders')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ds_order_items', function (Blueprint $table) {
            $table->dropForeign('ds_order_items_ds_order_id_foreign');
        });
    }
};
