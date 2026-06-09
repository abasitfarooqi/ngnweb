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
        Schema::create('vehicle_delivery_surcharges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->unique()->comment('Type of surcharge (e.g. motorcycle type fees, time surcharges, etc)');
            $table->decimal('percentage', 5)->nullable()->comment('Percentage surcharge to apply to the total delivery fee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_delivery_surcharges');
    }
};
