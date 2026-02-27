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
        if (! Schema::hasTable('inventory_histories')) {
            Schema::create('inventory_histories', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('stockable_type');
                $table->integer('stockable_id');
                $table->string('reference_type')->nullable();
                $table->integer('reference_id')->nullable();
                $table->integer('quantity');
                $table->integer('old_quantity');
                $table->string('event')->nullable();
                $table->string('description')->nullable();
                $table->integer('inventory_id');
                $table->integer('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_histories');
    }
};
