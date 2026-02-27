<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('mot_bookings')) {
            Schema::create('mot_bookings', function (Blueprint $table) {
                $table->string('title')->nullable();
                $table->dateTime('start')->nullable();
                $table->dateTime('end')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Schema::dropIfExists('mot_bookings');
    }
};
