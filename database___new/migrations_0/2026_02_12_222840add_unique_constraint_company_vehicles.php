<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $exists = DB::selectOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = "UNIQUE"',
            [DB::getDatabaseName(), 'company_vehicles', 'company_vehicles_motorbike_id_unique']
        );
        if ($exists) {
            return;
        }
        Schema::table('company_vehicles', function (Blueprint $table) {
            $table->unique(['motorbike_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $exists = DB::selectOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = "UNIQUE"',
            [DB::getDatabaseName(), 'company_vehicles', 'company_vehicles_motorbike_id_unique']
        );
        if (! $exists) {
            return;
        }
        Schema::table('company_vehicles', function (Blueprint $table) {
            $table->dropUnique(['motorbike_id']);
        });
    }
};
