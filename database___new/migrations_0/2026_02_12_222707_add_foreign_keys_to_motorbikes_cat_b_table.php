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
        Schema::table('motorbikes_cat_b', function (Blueprint $table) {
            $table->foreign(['branch_id'])->references(['id'])->on('branches')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['motorbike_id'])->references(['id'])->on('motorbikes')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorbikes_cat_b', function (Blueprint $table) {
            $table->dropForeign('motorbikes_cat_b_branch_id_foreign');
            $table->dropForeign('motorbikes_cat_b_motorbike_id_foreign');
        });
    }
};
