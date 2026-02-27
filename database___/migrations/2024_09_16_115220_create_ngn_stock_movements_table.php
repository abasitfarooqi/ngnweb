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
        Schema::create('ngn_stock_movements', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('branch_id')->nullable(false);
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');

            $table->dateTime('transaction_date')->nullable(); // Transaction Date

            $table->unsignedBigInteger('product_id')->nullable(false);
            $table->foreign('product_id')->references('id')->on('ngn_products')->onDelete('restrict');

            $table->decimal('in', 10, 2)->default(0.00); // IN (Getting stock from supplier)
            $table->decimal('out', 10, 2)->default(0.00); // OUT (transfer, or selling out)

            $table->string('transaction_type')->default('transaction_type'); // Transfer Branches, // Supplier, // Sales MOVEMENTS/transaction, Stock Adjustment 4 types

            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');

            $table->string('ref_doc_no')->nullable(); // Reference Document Number. (Transfer, Sale Invoice, Purchase Invoice, Stock Adjustment)
            $table->string('remarks')->nullable(); // Remarks, notes, etc.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_stock_movements');
    }
};
