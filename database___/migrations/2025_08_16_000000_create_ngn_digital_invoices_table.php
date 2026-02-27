<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ngn_digital_invoices', function (Blueprint $table) {
            $table->id();

            // Core identifiers
            $table->string('invoice_number')->unique(); // e.g. INV-2025-0001
            $table->enum('invoice_type', ['repair', 'rental', 'sale', 'service'])->index();
            $table->enum('invoice_category', ['new', 'used', 'parts', 'service'])->nullable()->index(); // only relevant if type = sale
            $table->string('template')->default('sale'); // modern|classic|minimal etc.

            // Customer details (can link to customers table if available, but stored here for snapshot)
            $table->unsignedBigInteger('customer_id')->nullable(); // optional FK if you want
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();

            // Vehicle context (from motorbikes table or entered manually)
            $table->unsignedBigInteger('motorbike_id')->nullable(); // optional FK
            $table->string('registration_number')->nullable();
            $table->string('vin')->nullable();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->year('year')->nullable();

            // Dates
            $table->date('issue_date')->index();
            $table->date('due_date')->nullable();

            // Totals
            $table->decimal('total', 12, 2)->default(0);

            // Notes
            $table->string('internal_notes')->nullable(); // internal, not visible to customer
            $table->string('notes')->nullable();     // visible to customer

            // Status tracking
            $table->enum('status', ['draft', 'approved', 'sent', 'paid', 'cancelled'])->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_digital_invoices');
    }
};
