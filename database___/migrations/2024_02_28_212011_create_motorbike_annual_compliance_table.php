<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('motorbike_annual_compliance')) {
            Schema::create('motorbike_annual_compliance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('motorbike_id')->constrained('motorbikes')->onDelete('cascade');
                $table->year('year');
                $table->string('mot_status');
                $table->string('road_tax_status');
                $table->string('insurance_status');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_annual_compliance');
    }
};
