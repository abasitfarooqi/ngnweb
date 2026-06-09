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
        Schema::create('renting_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('transaction_date')->nullable();
            $table->unsignedBigInteger('booking_id')->index('renting_transactions_booking_id_foreign');
            $table->unsignedBigInteger('invoice_id')->index('renting_transactions_invoice_id_foreign');
            $table->unsignedBigInteger('transaction_type_id')->index('renting_transactions_transaction_type_id_foreign');
            $table->unsignedBigInteger('payment_method_id')->index('renting_transactions_payment_method_id_foreign');
            $table->decimal('amount', 10)->default(0);
            $table->unsignedBigInteger('user_id')->index('renting_transactions_user_id_foreign');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renting_transactions');
    }
};
