<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('custodian', 50)->nullable(false);
            $table->unsignedBigInteger('motorbike_id')->nullable(false);
            $table->foreign('motorbike_id')->references('id')->on('motorbikes')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_vehicles');
    }
};
