<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $db = DB::getDatabaseName();
        $fkExists = DB::selectOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$db, 'renting_pricings', 'renting_pricings_motorbike_id_foreign']
        );
        if ($fkExists) {
            Schema::table('renting_pricings', function (Blueprint $table) {
                $table->dropForeign('renting_pricings_motorbike_id_foreign');
            });
        }

        $uniqueExists = DB::selectOne(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = "UNIQUE"',
            [$db, 'renting_pricings', 'renting_pricings_motorbike_id_iscurrent_unique']
        );
        if ($uniqueExists) {
            Schema::table('renting_pricings', function (Blueprint $table) {
                $table->dropUnique('renting_pricings_motorbike_id_iscurrent_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If you're rolling back the migration, you need to recreate the foreign key constraint and the unique index

        Schema::table('renting_pricings', function (Blueprint $table) {
            $table->foreign('motorbike_id')->references('id')->on('motorbikes');
            $table->unique(['motorbike_id', 'iscurrent'], 'renting_pricings_motorbike_id_iscurrent_unique');
        });
    }
};
