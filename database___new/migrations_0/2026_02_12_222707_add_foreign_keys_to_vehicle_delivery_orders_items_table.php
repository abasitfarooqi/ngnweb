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
        Schema::table('vehicle_delivery_orders_items', function (Blueprint $table) {
            $table->foreign(['drop_branch_id'])->references(['id'])->on('branches')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['vehicle_delivery_order_id'])->references(['id'])->on('vehicle_delivery_orders')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_delivery_orders_items', function (Blueprint $table) {
            $table->dropForeign('vehicle_delivery_orders_items_drop_branch_id_foreign');
            $table->dropForeign('vehicle_delivery_orders_items_vehicle_delivery_order_id_foreign');
        });
    }
};
