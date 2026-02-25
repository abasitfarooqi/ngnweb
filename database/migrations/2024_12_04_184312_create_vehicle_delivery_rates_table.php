<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_delivery_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('base_fee', 8, 2)->comment('Flat starting fee £20.00');
            $table->decimal('per_mile_fee', 8, 2)->comment('Cost per mile beyond the base distance £1.50');
            $table->decimal('base_distance', 8, 2)->comment('Distance covered by the base fee 5 miles');
            $table->timestamps();
        });

        DB::table('vehicle_delivery_rates')->insert([
            ['base_fee' => 20.00, 'per_mile_fee' => 1.50, 'base_distance' => 5.00, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_delivery_rates');
    }
};
