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
        Schema::create('delivery_vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Name of the bike type (e.g., "Standard", "Mid-Range")');
            $table->string('cc_range')->comment('Engine range (e.g., "0-125cc", "126-600cc", "601cc+")');
            $table->decimal('additional_fee', 8, 2)->comment('Extra fee for this type');
            $table->timestamps();
        });

        DB::table('delivery_vehicle_types')->insert([
            ['name' => 'Standard', 'cc_range' => '0-125cc', 'additional_fee' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mid-Range', 'cc_range' => '126-600cc', 'additional_fee' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Heavy/Dual', 'cc_range' => '601cc+', 'additional_fee' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_vehicle_types');
    }
};
