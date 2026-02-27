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
        Schema::create('addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('street_address')->nullable()->default('');
            $table->string('street_address_plus')->nullable();
            $table->string('post_code')->nullable()->default('');
            $table->string('city')->nullable()->default('');
            $table->string('phone_number')->nullable();
            $table->boolean('is_default')->default(false);
            $table->enum('type', ['billing', 'shipping']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
