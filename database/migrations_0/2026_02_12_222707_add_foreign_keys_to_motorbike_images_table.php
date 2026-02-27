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
        Schema::table('motorbike_images', function (Blueprint $table) {
            $table->foreign(['motorbike_id'])->references(['id'])->on('motorbikes')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorbike_images', function (Blueprint $table) {
            $table->dropForeign('motorbike_images_motorbike_id_foreign');
        });
    }
};
