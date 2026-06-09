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
        Schema::create('purchase_used_vehicles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('purchase_date')->default('CURRENT_TIMESTAMP');
            $table->string('full_name');
            $table->string('address');
            $table->string('postcode');
            $table->string('phone_number');
            $table->string('email');
            $table->string('make');
            $table->string('year');
            $table->string('colour');
            $table->string('fuel_type');
            $table->string('model');
            $table->string('reg_no');
            $table->integer('current_mileage')->default(0);
            $table->string('vin');
            $table->string('engine_number')->nullable();
            $table->decimal('price')->default(0);
            $table->decimal('deposit')->default(0);
            $table->decimal('outstanding')->default(0);
            $table->decimal('total_to_pay')->default(0);
            $table->string('account_name')->nullable();
            $table->string('sort_code')->nullable();
            $table->string('account_number')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable()->index('purchase_used_vehicles_user_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_used_vehicles');
    }
};
