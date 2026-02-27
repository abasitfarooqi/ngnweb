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
        Schema::create('ngn_digital_invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('invoice_number')->unique();
            $table->enum('invoice_type', ['repair', 'rental', 'sale', 'service'])->index();
            $table->enum('invoice_category', ['new', 'used', 'parts', 'service'])->nullable()->index();
            $table->string('template')->default('sale');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->unsignedBigInteger('motorbike_id')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('vin')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->year('year')->nullable();
            $table->date('issue_date')->index();
            $table->date('due_date')->nullable();
            $table->decimal('total', 12)->default(0);
            $table->decimal('amount', 10)->nullable();
            $table->decimal('total_paid', 10)->nullable();
            $table->unsignedBigInteger('booking_invoice_id')->nullable()->index('ngn_digital_invoices_booking_invoice_id_foreign');
            $table->string('internal_notes')->nullable();
            $table->string('notes')->nullable();
            $table->enum('status', ['draft', 'approved', 'sent', 'paid', 'cancelled'])->default('draft')->index();
            $table->unsignedBigInteger('created_by')->nullable()->index('ngn_digital_invoices_created_by_foreign');
            $table->timestamps();
            $table->string('whatsapp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ngn_digital_invoices');
    }
};
