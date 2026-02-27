<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorbikes_cat_b', function (Blueprint $table) {
            $table->id();
            $table->date('dop')->nullable(false);
            $table->unsignedBigInteger('motorbike_id')->nullable(false);
            $table->foreign('motorbike_id')->references('id')->on('motorbikes')->onDelete('restrict');
            $table->text('notes')->nullable(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbikes_cat_b');
    }
};
