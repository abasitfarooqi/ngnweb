<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('motorbikes_sale')) {
            Schema::create('motorbikes_sale', function (Blueprint $table) {
                $table->bigIncrements('id');

                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('condition')->default('-');
                $table->unsignedBigInteger('motorbike_id')->index();

                $table->timestamps();

                $table->decimal('mileage', 10, 2)->default(0);
                $table->date('date_of_purchase')->default('2024-04-24');
                $table->date('date_of_sale')->default('2024-04-24');
                $table->decimal('price', 10, 2)->default(0);

                $table->string('engine')->default('NOT CHECKED');
                $table->string('suspension')->default('NOT CHECKED');
                $table->string('brakes')->default('NOT CHECKED');
                $table->string('belt')->default('NOT CHECKED');
                $table->string('electrical')->default('NOT CHECKED');
                $table->string('tires')->default('NOT CHECKED');

                $table->text('note')->nullable(); // safer for seeding
                $table->boolean('v5_available')->nullable()->default(true);
                $table->boolean('is_sold')->default(false);

                $table->string('image_one')->nullable();
                $table->string('image_two')->nullable();
                $table->string('image_three')->nullable();
                $table->string('image_four')->nullable();

                $table->text('accessories')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('motorbikes_sale');
    }
};
