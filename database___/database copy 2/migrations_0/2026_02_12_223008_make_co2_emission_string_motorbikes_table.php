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
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->string('co2_emissions')->change();
        });
    }

    public function down(): void
    {
        Schema::table('motorbikes', function (Blueprint $table) {
            $table->integer('co2_emissions')->change();
        });
    }
};
