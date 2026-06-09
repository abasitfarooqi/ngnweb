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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('last_name')->default('');
            $table->string('first_name')->default('');
            $table->string('company_name')->nullable();
            $table->string('street_address')->default('');
            $table->string('street_address_plus')->nullable();
            $table->string('postcode')->default('');
            $table->string('city')->default('');
            $table->string('phone_number')->nullable();
            $table->boolean('is_default')->default(false);
            $table->enum('type', ['billing', 'shipping', 'office', 'other']);
            $table->unsignedBigInteger('country_id')->index('customer_addresses_country_id_foreign');
            $table->unsignedBigInteger('customer_id')->index('customer_addresses_customer_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
