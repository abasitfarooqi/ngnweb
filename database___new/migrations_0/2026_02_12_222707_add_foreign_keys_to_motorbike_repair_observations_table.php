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
        Schema::table('motorbike_repair_observations', function (Blueprint $table) {
            $table->foreign(['motorbike_repair_id'])->references(['id'])->on('motorbikes_repair')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorbike_repair_observations', function (Blueprint $table) {
            $table->dropForeign('motorbike_repair_observations_motorbike_repair_id_foreign');
        });
    }
};
