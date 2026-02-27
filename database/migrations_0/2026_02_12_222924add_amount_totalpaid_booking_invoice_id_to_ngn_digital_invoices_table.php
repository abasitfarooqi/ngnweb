<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ngn_digital_invoices', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->nullable()->after('total');
            $table->decimal('total_paid', 10, 2)->nullable()->after('amount');
            $table->unsignedBigInteger('booking_invoice_id')->nullable()->after('total_paid');

            $table->foreign('booking_invoice_id')
                ->references('id')
                ->on('booking_invoices');
        });
    }

    public function down(): void
    {
        Schema::table('ngn_digital_invoices', function (Blueprint $table) {
            $table->dropForeign(['booking_invoice_id']);
            $table->dropColumn(['amount', 'total_paid', 'booking_invoice_id']);
        });
    }
};
