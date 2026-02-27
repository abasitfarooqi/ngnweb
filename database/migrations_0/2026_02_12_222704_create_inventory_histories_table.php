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
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('stockable_type');
            $table->unsignedBigInteger('stockable_id');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->integer('quantity');
            $table->integer('old_quantity')->default(0);
            $table->text('event')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('inventory_id')->index();
            $table->unsignedBigInteger('user_id')->index();

            $table->index(['reference_type', 'reference_id']);
            $table->index(['stockable_type', 'stockable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_histories');
    }
};
