<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ngn_digital_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('ngn_digital_invoices')->index();

            // ✅ Item details
            $table->string('item_name'); // human-readable
            $table->string('sku')->nullable()->index(); // allow filtering/search by SKU
            $table->unsignedInteger('quantity')->default(1); // never negative
            $table->decimal('price', 12, 2)->default(0); // unit price
            $table->decimal('discount', 12, 2)->default(0); // optional improvement
            $table->decimal('tax', 12, 2)->default(0);      // optional improvement

            // ✅ Store line total explicitly (quantity * price - discount + tax)
            // Useful for snapshots even if pricing rules change later
            $table->decimal('total', 12, 2)->default(0);

            $table->string('notes')->nullable(); // optional notes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ngn_digital_invoice_items');
    }
};
