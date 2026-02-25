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
        Schema::create('motorbike_repair_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorbike_repair_id')->constrained('motorbikes_repair'); // Ensure the table name matches
            $table->string('observation_description', 255); // Specified length for varchar
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbike_repair_observations');
    }
};
