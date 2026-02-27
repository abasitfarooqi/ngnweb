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
        Schema::create('delivery_vehicle_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique()->comment('Name of the bike type (e.g., "Standard", "Mid-Range")');
            $table->string('cc_range')->comment('Engine range (e.g., "0-125cc", "126-600cc", "601cc+")');
            $table->decimal('additional_fee')->comment('Extra fee for this type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_vehicle_types');
    }
};
