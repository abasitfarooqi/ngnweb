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
        Schema::create('product_has_relations', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->string('productable_type');
            $table->unsignedBigInteger('productable_id');
            $table->unsignedBigInteger('stock_id');

            $table->index(['productable_type', 'productable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_has_relations');
    }
};
