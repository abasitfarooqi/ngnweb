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
        Schema::table('delivery_agreement_accesses', function (Blueprint $table) {
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['enquiry_id'])->references(['id'])->on('motorbike_delivery_order_enquiries')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_agreement_accesses', function (Blueprint $table) {
            $table->dropForeign('delivery_agreement_accesses_customer_id_foreign');
            $table->dropForeign('delivery_agreement_accesses_enquiry_id_foreign');
        });
    }
};
