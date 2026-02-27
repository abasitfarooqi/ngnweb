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
            $table->id();
            $table->unsignedBigInteger('vehicle_delivery_order_id');
            $table->foreign('vehicle_delivery_order_id')->references('id')->on('vehicle_delivery_orders')->onDelete('restrict');
            $table->decimal('pickup_point_coordinates_lat', 10, 7);
            $table->decimal('pickup_point_coordinates_lon', 10, 7);
            $table->unsignedBigInteger('drop_branch_id');
            $table->foreign('drop_branch_id')->references('id')->on('branches')->onDelete('restrict');
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
