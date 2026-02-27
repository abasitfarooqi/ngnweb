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
        Schema::create('motorbike_repair_services_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('price', 10, 2)->nullable()->default(0);
            $table->timestamps();
        });

        // seed all initial services
        $services = [
            'Engine Oil Replacement',
            'Oil Filter Replacement',
            'Air Filter Cleaning & Replacement',
            'Spark Plug Inspection & Replacement',
            'Chain Cleaning, lubricating, and adjusting the chain',
            'Belt inspection for wear',
            'Gearbox Oil Inspection & Replacement',
            'Clutch Adjustment',
            'Brake Check Inspection',
            'Brake Fluid Check & Top-up',
            'Brake Operation Test',
            'Brake Pads/Discs Inspection',
            'Brake Fluid Replacement',
            'Brake Calipers Inspection & Cleaning',
            'Tire Pressure Check',
            'Tire Inspection for Tread Wear or Damage',
            'Wheel Bearings Check',
            'Chain Lubrication',
            'Chain Check & Adjustment for Proper Operation',
            'Drive Belt Inspection',
            'Drive Belt Wear Check',
            'Fork Seal Check for Leakage',
            'Steering Head Bearings Inspection',
            'Shock Absorbers Inspection',
            'Lights Checking & Inspection',
            'Battery Check',
            'Horn Test',
            'Wiring Inspection',
            'Checking Coolant Level',
            'Clutch & Throttle Cable Inspection',
            'Coolant Replacement',
            'Checking & Tightening Loose Bolts & Nuts',
            'Frame Inspection',
            'Inspecting the coolant level and replacing if needed.',
            'Radiator checking for blockages, leaks, or damage.',
            'Inspecting for Leaks, Rust, or Damage',
            'Checking All Mountings & Brackets',
            'Checking and tightening loose bolts and nuts.',
            'Inspecting for leaks, rust, or visible damage.',
            'Throttle and Clutch Cable Adjustment.',
            'Test Ride',
            'Software Updates',
        ];

        foreach ($services as $service) {
            DB::table('motorbike_repair_services_lists')->insert([
                'name' => $service,
                'description' => null,
                'price' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbike_repair_services_lists');
    }
};
