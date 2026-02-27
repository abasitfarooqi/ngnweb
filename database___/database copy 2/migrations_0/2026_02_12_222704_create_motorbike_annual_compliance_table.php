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
        Schema::create('motorbike_annual_compliance', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('motorbike_id')->index('motorbike_annual_compliance_motorbike_id_foreign');
            $table->year('year');
            $table->string('mot_status', 100);
            $table->string('road_tax_status', 100);
            $table->string('insurance_status', 100);
            $table->timestamps();
            $table->date('tax_due_date')->nullable();
            $table->date('insurance_due_date')->nullable();
            $table->date('mot_due_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbike_annual_compliance');
    }
};
