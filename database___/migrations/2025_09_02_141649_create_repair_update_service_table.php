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
        Schema::create('repair_update_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('update_id')->constrained('motorbike_repair_updates')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('motorbike_repair_services_lists')->cascadeOnDelete();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_update_service');
    }
};
