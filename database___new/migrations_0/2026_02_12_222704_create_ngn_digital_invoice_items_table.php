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
        Schema::create('ngn_digital_invoice_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('invoice_id')->index('1');
            $table->string('item_name');
            $table->string('sku')->nullable()->index();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 12)->default(0);
            $table->decimal('discount', 12)->default(0);
            $table->decimal('tax', 12)->default(0);
            $table->decimal('total', 12)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_digital_invoice_items');
    }
};
