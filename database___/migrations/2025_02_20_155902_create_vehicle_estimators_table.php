<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_estimators', function (Blueprint $table) {
            $table->id();
            $table->string('referer_id')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('vrm')->nullable();
            $table->integer('engine_size')->nullable();
            $table->integer('mileage')->nullable();
            $table->string('vehicle_year')->nullable();
            $table->integer('condition')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('calculated_value', 10, 2)->nullable();
            $table->boolean('like')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_estimators');
    }
};
