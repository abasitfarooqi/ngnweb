<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('renting_free_week_awards')) {
            return;
        }

        Schema::create('renting_free_week_awards', function (Blueprint $table): void {
            $table->id();
            $table->string('source', 16);
            $table->foreignId('referral_id')->nullable()->constrained('renting_referrals')->nullOnDelete();
            $table->foreignId('awarded_booking_id')->constrained('renting_bookings')->restrictOnDelete();
            $table->foreignId('awarded_invoice_id')->constrained('booking_invoices')->restrictOnDelete();
            $table->foreignId('awarded_transaction_id')->nullable()->constrained('renting_transactions')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->foreignId('hirer_customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('selected_referrer_customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('selected_referrer_booking_id')->nullable()->constrained('renting_bookings')->nullOnDelete();
            $table->json('selected_paid_invoices')->nullable();
            $table->text('eligibility_note')->nullable();
            $table->text('staff_proof')->nullable();
            $table->foreignId('applied_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('awarded_invoice_id');
            $table->index(['awarded_booking_id', 'source']);
            $table->index('selected_referrer_customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('renting_free_week_awards');
    }
};
