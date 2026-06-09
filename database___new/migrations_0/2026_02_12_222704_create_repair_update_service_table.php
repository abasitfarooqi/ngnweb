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
        Schema::create('repair_update_service', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('update_id')->index('repair_update_service_update_id_foreign');
            $table->unsignedBigInteger('service_id')->index('repair_update_service_service_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repair_update_service');
    }
};
