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
        Schema::table('motorbike_repair_observations', function (Blueprint $table) {
            $table->string('observation_description', 3000)->change(); // Max safe limit for utf8mb4
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorbike_repair_observations', function (Blueprint $table) {
            $table->string('observation_description', 255)->change(); // Rollback to previous limit
        });
    }
};
