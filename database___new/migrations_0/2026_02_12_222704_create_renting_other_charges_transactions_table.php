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
        Schema::create('renting_other_charges_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('transaction_date');
            $table->unsignedBigInteger('charges_id')->index('renting_other_charges_transactions_charges_id_foreign');
            $table->unsignedBigInteger('transaction_type_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->decimal('amount');
            $table->unsignedBigInteger('user_id')->index('renting_other_charges_transactions_user_id_foreign');
            $table->string('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('renting_other_charges_transactions');
    }
};
