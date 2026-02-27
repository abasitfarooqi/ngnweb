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
        Schema::create('motorbikes_sale', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable()->index('motorbikes_sale_user_id_foreign');
            $table->string('condition')->default('-');
            $table->unsignedBigInteger('motorbike_id')->index('motorbikes_sale_motorbike_id_foreign');
            $table->timestamps();
            $table->decimal('mileage')->default(0);
            $table->date('date_of_purchase')->default('2024-04-24');
            $table->date('date_of_sale')->default('2024-04-24');
            $table->decimal('price')->default(0);
            $table->string('engine')->default('NOT CHECKED');
            $table->string('suspension')->default('NOT CHECKED');
            $table->string('brakes')->default('NOT CHECKED');
            $table->string('belt')->default('NOT CHECKED');
            $table->string('electrical')->default('NOT CHECKED');
            $table->string('tires')->default('NOT CHECKED');
            $table->text('note');
            $table->boolean('v5_available')->nullable()->default(true);
            $table->boolean('is_sold')->default(false);
            $table->string('image_one')->nullable();
            $table->string('image_two')->nullable();
            $table->string('image_three')->nullable();
            $table->string('image_four')->nullable();
            $table->text('accessories')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorbikes_sale');
    }
};
