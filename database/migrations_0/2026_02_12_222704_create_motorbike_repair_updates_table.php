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
        Schema::create('motorbike_repair_updates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('motorbike_repair_id')->index('motorbike_repair_updates_motorbike_repair_id_foreign');
            $table->text('job_description');
            $table->decimal('price');
            $table->text('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbike_repair_updates');
    }
};
