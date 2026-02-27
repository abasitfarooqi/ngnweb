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
        Schema::table('repair_update_service', function (Blueprint $table) {
            $table->foreign(['service_id'])->references(['id'])->on('motorbike_repair_services_lists')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['update_id'])->references(['id'])->on('motorbike_repair_updates')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('repair_update_service', function (Blueprint $table) {
            $table->dropForeign('repair_update_service_service_id_foreign');
            $table->dropForeign('repair_update_service_update_id_foreign');
        });
    }
};
