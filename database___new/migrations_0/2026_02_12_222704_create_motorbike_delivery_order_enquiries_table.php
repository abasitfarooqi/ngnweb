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
        Schema::create('motorbike_delivery_order_enquiries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_id')->nullable();
            $table->string('pickup_address')->nullable();
            $table->string('pickup_postcode', 10)->nullable();
            $table->string('dropoff_address')->nullable();
            $table->string('dropoff_postcode', 10)->nullable();
            $table->string('vrm')->nullable();
            $table->boolean('moveable')->nullable();
            $table->boolean('documents')->nullable();
            $table->boolean('keys')->nullable();
            $table->dateTime('pick_up_datetime')->nullable();
            $table->double('distance')->nullable();
            $table->text('note')->nullable();
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_postcode')->nullable();
            $table->double('total_cost')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->unsignedBigInteger('vehicle_type_id')->nullable();
            $table->string('branch_name')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->boolean('is_dealt')->nullable()->default(false);
            $table->unsignedBigInteger('dealt_by_user_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbike_delivery_order_enquiries');
    }
};
