<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, drop the foreign key constraint
        Schema::table('renting_pricings', function (Blueprint $table) {
            $table->dropForeign('renting_pricings_motorbike_id_foreign');
        });

        // Then, drop the unique index
        Schema::table('renting_pricings', function (Blueprint $table) {
            $table->dropUnique('renting_pricings_motorbike_id_iscurrent_unique');
        });
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
