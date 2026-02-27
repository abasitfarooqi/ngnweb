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
        Schema::table('customer_auths', function (Blueprint $table) {
            $table->foreign(['current_terms_version_id'])->references(['id'])->on('terms_versions')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_auths', function (Blueprint $table) {
            $table->dropForeign('customer_auths_current_terms_version_id_foreign');
            $table->dropForeign('customer_auths_customer_id_foreign');
        });
    }
};
