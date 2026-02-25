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
        Schema::table('motorbike_delivery_order_enquiries', function (Blueprint $table) {
            $table->string('pickup_postcode', 10)->nullable()->after('pickup_address');
            $table->string('dropoff_postcode', 10)->nullable()->after('dropoff_address');
            $table->unsignedBigInteger('vehicle_type_id')->nullable()->after('vehicle_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorbike_delivery_order_enquiries', function (Blueprint $table) {
            $table->dropColumn(['pickup_postcode', 'dropoff_postcode', 'vehicle_type_id']);
        });
    }
};
