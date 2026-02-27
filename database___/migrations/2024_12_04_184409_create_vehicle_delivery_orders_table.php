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
        Schema::create('vehicle_delivery_orders', function (Blueprint $table) {
            $table->id(); // // quote number
            $table->datetime('quote_date');
            $table->datetime('pickup_date');
            $table->string('vrm');
            $table->string('full_name');
            $table->string('phone_number');
            $table->decimal('total_distance', 8, 2);
            $table->decimal('surcharge', 8, 2);
            $table->unsignedBigInteger('delivery_vehicle_type_id');
            $table->foreign('delivery_vehicle_type_id')->references('id')->on('delivery_vehicle_types')->onDelete('restrict');
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_delivery_orders');
    }
};
