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
        Schema::table('renting_transactions', function (Blueprint $table) {
            $table->foreign(['booking_id'])->references(['id'])->on('renting_bookings')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['invoice_id'])->references(['id'])->on('booking_invoices')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['payment_method_id'])->references(['id'])->on('payment_methods')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['transaction_type_id'])->references(['id'])->on('transaction_types')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('renting_transactions', function (Blueprint $table) {
            $table->dropForeign('renting_transactions_booking_id_foreign');
            $table->dropForeign('renting_transactions_invoice_id_foreign');
            $table->dropForeign('renting_transactions_payment_method_id_foreign');
            $table->dropForeign('renting_transactions_transaction_type_id_foreign');
            $table->dropForeign('renting_transactions_user_id_foreign');
        });
    }
};
