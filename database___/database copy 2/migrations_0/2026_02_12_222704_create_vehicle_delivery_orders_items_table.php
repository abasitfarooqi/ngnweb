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
        Schema::create('vehicle_delivery_orders_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('vehicle_delivery_order_id')->index('vehicle_delivery_orders_items_vehicle_delivery_order_id_foreign');
            $table->decimal('pickup_point_coordinates_lat', 10, 7);
            $table->decimal('pickup_point_coordinates_lon', 10, 7);
            $table->unsignedBigInteger('drop_branch_id')->index('vehicle_delivery_orders_items_drop_branch_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_delivery_orders_items');
    }
};
