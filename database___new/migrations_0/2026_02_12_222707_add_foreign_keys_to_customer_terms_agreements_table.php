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
        Schema::table('customer_terms_agreements', function (Blueprint $table) {
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['terms_version_id'])->references(['id'])->on('terms_versions')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_terms_agreements', function (Blueprint $table) {
            $table->dropForeign('customer_terms_agreements_customer_id_foreign');
            $table->dropForeign('customer_terms_agreements_terms_version_id_foreign');
        });
    }
};
