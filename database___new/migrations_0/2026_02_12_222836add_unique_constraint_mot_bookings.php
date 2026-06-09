<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::selectOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = "UNIQUE"',
            [DB::getDatabaseName(), 'mot_bookings', 'start_end_unique']
        );
        if ($exists) {
            return;
        }
        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->unique(['start', 'end'], 'start_end_unique');
        });
    }

    public function down(): void
    {
        $exists = DB::selectOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = "UNIQUE"',
            [DB::getDatabaseName(), 'mot_bookings', 'start_end_unique']
        );
        if (! $exists) {
            return;
        }
        Schema::table('mot_bookings', function (Blueprint $table) {
            $table->dropUnique('start_end_unique');
        });
    }
};
