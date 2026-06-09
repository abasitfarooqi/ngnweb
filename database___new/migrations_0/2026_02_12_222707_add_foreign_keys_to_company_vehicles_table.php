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
        Schema::table('company_vehicles', function (Blueprint $table) {
            $table->foreign(['motorbike_id'])->references(['id'])->on('motorbikes')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_vehicles', function (Blueprint $table) {
            $table->dropForeign('company_vehicles_motorbike_id_foreign');
        });
    }
};
