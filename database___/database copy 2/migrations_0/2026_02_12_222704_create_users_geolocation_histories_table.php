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
        Schema::create('users_geolocation_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('ip_api')->nullable();
            $table->string('extreme_ip_lookup')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_geolocation_histories');
    }
};
