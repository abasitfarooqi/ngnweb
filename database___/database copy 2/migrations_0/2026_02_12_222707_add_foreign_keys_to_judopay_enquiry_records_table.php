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
        Schema::table('judopay_enquiry_records', function (Blueprint $table) {
            $table->foreign(['payment_session_outcome_id'])->references(['id'])->on('judopay_payment_session_outcomes')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('judopay_enquiry_records', function (Blueprint $table) {
            $table->dropForeign('judopay_enquiry_records_payment_session_outcome_id_foreign');
        });
    }
};
