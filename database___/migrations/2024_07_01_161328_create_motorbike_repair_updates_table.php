<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorbike_repair_updates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('motorbike_repair_id');
            $table->text('job_description');
            $table->decimal('price', 8, 2);
            $table->text('note');
            $table->timestamps();

            $table->foreign('motorbike_repair_id')->references('id')->on('motorbikes_repair')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_repair_updates');
    }
};
