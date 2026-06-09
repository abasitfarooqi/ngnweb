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
        Schema::table('ngn_digital_invoice_items', function (Blueprint $table) {
            $table->foreign(['invoice_id'], '1')->references(['id'])->on('ngn_digital_invoices')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ngn_digital_invoice_items', function (Blueprint $table) {
            $table->dropForeign('1');
        });
    }
};
