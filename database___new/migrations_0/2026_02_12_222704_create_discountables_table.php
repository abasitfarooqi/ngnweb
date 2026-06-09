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
        Schema::create('discountables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('condition')->nullable();
            $table->unsignedInteger('total_use')->default(0);
            $table->string('discountable_type');
            $table->unsignedBigInteger('discountable_id');
            $table->unsignedBigInteger('discount_id')->index();

            $table->index(['discountable_type', 'discountable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discountables');
    }
};
