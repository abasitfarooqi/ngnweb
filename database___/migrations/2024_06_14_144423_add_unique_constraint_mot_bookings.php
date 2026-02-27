<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->unique(['start', 'end'], 'start_end_unique');
        });
    }

    public function down(): void
    {
        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->dropUnique('start_end_unique');
        });
    }
};
