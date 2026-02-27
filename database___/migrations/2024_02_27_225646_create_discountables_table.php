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
        if (! Schema::hasTable('discountables')) {
            Schema::create('discountables', function (Blueprint $table) {
                $table->id();
                $table->string('condition')->nullable();
                $table->integer('total_use');
                $table->string('discountable_type');
                $table->integer('discountable_id');
                $table->integer('discount_id');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discountables');
    }
};
