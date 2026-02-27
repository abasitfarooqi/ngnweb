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
        Schema::create('vehicle_delivery_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->decimal('base_fee')->comment('Flat starting fee £20.00');
            $table->decimal('per_mile_fee')->comment('Cost per mile beyond the base distance £1.50');
            $table->decimal('base_distance')->comment('Distance covered by the base fee 5 miles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_delivery_rates');
    }
};
