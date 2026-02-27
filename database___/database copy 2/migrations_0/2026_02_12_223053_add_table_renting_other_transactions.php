<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// use app\Models\RentingOtherChargesTransaction;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('renting_other_charges_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->unsignedBigInteger('charges_id');
            $table->foreign('charges_id')->references('id')->on('renting_other_charges')->onDelete('restrict');
            $table->unsignedBigInteger('transaction_type_id');
            $table->unsignedBigInteger('payment_method_id');
            $table->decimal('amount', 8, 2);
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->string('notes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renting_other_charges_transactions');
    }
};
