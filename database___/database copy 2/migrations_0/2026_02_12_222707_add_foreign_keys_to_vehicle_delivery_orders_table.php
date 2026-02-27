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
        Schema::table('vehicle_delivery_orders', function (Blueprint $table) {
            $table->foreign(['branch_id'])->references(['id'])->on('branches')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['delivery_vehicle_type_id'])->references(['id'])->on('delivery_vehicle_types')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_delivery_orders', function (Blueprint $table) {
            $table->dropForeign('vehicle_delivery_orders_branch_id_foreign');
            $table->dropForeign('vehicle_delivery_orders_delivery_vehicle_type_id_foreign');
            $table->dropForeign('vehicle_delivery_orders_user_id_foreign');
        });
    }
};
