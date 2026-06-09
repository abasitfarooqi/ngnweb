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
            $table->bigIncrements('id');
            $table->dateTime('quote_date');
            $table->dateTime('pickup_date');
            $table->string('vrm');
            $table->string('full_name');
            $table->string('phone_number');
            $table->decimal('total_distance');
            $table->decimal('surcharge');
            $table->unsignedBigInteger('delivery_vehicle_type_id')->index('vehicle_delivery_orders_delivery_vehicle_type_id_foreign');
            $table->unsignedBigInteger('branch_id')->index('vehicle_delivery_orders_branch_id_foreign');
            $table->unsignedBigInteger('user_id')->index('vehicle_delivery_orders_user_id_foreign');
            $table->timestamps();
            $table->string('notes')->nullable();
            $table->string('email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_delivery_orders');
    }
};
