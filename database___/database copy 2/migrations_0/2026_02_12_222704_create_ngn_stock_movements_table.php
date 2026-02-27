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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('branch_id')->index('ngn_stock_movements_branch_id_foreign');
            $table->dateTime('transaction_date')->nullable();
            $table->unsignedBigInteger('product_id')->index('ngn_stock_movements_product_id_foreign');
            $table->decimal('in', 10)->default(0);
            $table->decimal('out', 10)->default(0);
            $table->string('transaction_type')->default('transaction_type');
            $table->unsignedBigInteger('user_id')->index('ngn_stock_movements_user_id_foreign');
            $table->string('ref_doc_no')->nullable();
            $table->string('remarks')->nullable();
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
