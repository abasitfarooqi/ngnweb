<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorbikes_repair', function (Blueprint $table) {
            $table->id();
            $table->datetime('arrival_date')->nullable(false);
            $table->unsignedBigInteger('motorbike_id')->nullable(false);
            $table->foreign('motorbike_id')->references('id')->on('motorbikes')->onDelete('restrict');
            $table->text('notes')->nullable(false);
            $table->boolean('is_repaired')->default(false);
            $table->datetime('repaired_date')->nullable();
            $table->boolean('is_returned')->default(false);
            $table->datetime('returned_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbikes_repair');
    }
};
