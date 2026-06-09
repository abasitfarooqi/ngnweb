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
        Schema::table('customer_contracts', function (Blueprint $table) {
            $table->foreign(['application_id'])->references(['id'])->on('finance_applications')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['document_type_id'])->references(['id'])->on('document_types')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_contracts', function (Blueprint $table) {
            $table->dropForeign('customer_contracts_application_id_foreign');
            $table->dropForeign('customer_contracts_customer_id_foreign');
            $table->dropForeign('customer_contracts_document_type_id_foreign');
        });
    }
};
