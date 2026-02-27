<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motorbike_sale_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('motorbike_id');
            $table->unsignedBigInteger('motorbikes_sale_id');
            $table->unsignedBigInteger('user_id');
            $table->string('username');
            $table->string('reg_no')->nullable();
            $table->boolean('is_sold'); // true or false
            $table->timestamps();

            $table->foreign('motorbike_id')->references('id')->on('motorbikes')->cascadeOnDelete();
            $table->foreign('motorbikes_sale_id')->references('id')->on('motorbikes_sale')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbike_sale_logs');
    }
};
