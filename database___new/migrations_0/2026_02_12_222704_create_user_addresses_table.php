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
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('last_name')->nullable()->default('');
            $table->string('first_name')->nullable()->default('');
            $table->string('company_name')->nullable();
            $table->string('street_address')->nullable()->default('');
            $table->string('street_address_plus')->nullable();
            $table->string('zipcode')->nullable()->default('');
            $table->string('city')->nullable()->default('');
            $table->string('phone_number')->nullable();
            $table->boolean('is_default')->nullable()->default(false);
            $table->enum('type', ['billing', 'shipping']);
            $table->unsignedBigInteger('country_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
