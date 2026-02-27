<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('motorbikes')) {
            Schema::create('motorbikes', function (Blueprint $table) {
                $table->id();
                $table->string('vin_number')->unique();
                $table->string('make');
                $table->string('model');
                $table->year('year');
                $table->string('engine');
                $table->string('color');
                $table->unsignedBigInteger('created_by');
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbikes');
    }
};
