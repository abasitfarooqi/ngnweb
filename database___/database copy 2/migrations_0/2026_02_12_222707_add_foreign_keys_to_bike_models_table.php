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
        Schema::table('bike_models', function (Blueprint $table) {
            $table->foreign(['brand_name_id'])->references(['id'])->on('makes')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bike_models', function (Blueprint $table) {
            $table->dropForeign('bike_models_brand_name_id_foreign');
        });
    }
};
