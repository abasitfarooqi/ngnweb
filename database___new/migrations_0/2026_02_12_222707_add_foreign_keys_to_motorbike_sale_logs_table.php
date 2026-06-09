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
        Schema::table('motorbike_sale_logs', function (Blueprint $table) {
            $table->foreign(['motorbikes_sale_id'])->references(['id'])->on('motorbikes_sale')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['motorbike_id'])->references(['id'])->on('motorbikes')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorbike_sale_logs', function (Blueprint $table) {
            $table->dropForeign('motorbike_sale_logs_motorbikes_sale_id_foreign');
            $table->dropForeign('motorbike_sale_logs_motorbike_id_foreign');
        });
    }
};
