<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('motorbikes_sale', function (Blueprint $table) {
            $table->id();
            $table->string('condition')->default('-');
            $table->unsignedBigInteger('motorbike_id');
            $table->foreign('motorbike_id')->references('id')->on('motorbikes')->onDelete('restrict');
            
            // Additional columns needed by the application
            $table->boolean('is_sold')->default(false);
            $table->decimal('price', 8, 2)->default(0.00);
            $table->string('image_one')->nullable();
            $table->string('image_two')->nullable();
            $table->string('image_three')->nullable();
            $table->string('image_four')->nullable();
            
            // Additional sale details
            $table->decimal('mileage', 8, 2)->default(0.00);
            $table->date('date_of_purchase')->nullable();
            $table->date('date_of_sale')->nullable();
            $table->string('engine')->default('NOT CHECKED');
            $table->string('suspension')->default('NOT CHECKED');
            $table->string('brakes')->default('NOT CHECKED');
            $table->string('belt')->default('NOT CHECKED');
            $table->string('electrical')->default('NOT CHECKED');
            $table->string('tires')->default('NOT CHECKED');
            $table->text('note')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('motorbikes_sale');
    }
};
