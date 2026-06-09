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
        Schema::table('pcn_email_jobs', function (Blueprint $table) {
            $table->foreign(['case_id'])->references(['id'])->on('pcn_cases')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['motorbike_id'])->references(['id'])->on('motorbikes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcn_email_jobs', function (Blueprint $table) {
            $table->dropForeign('pcn_email_jobs_case_id_foreign');
            $table->dropForeign('pcn_email_jobs_customer_id_foreign');
            $table->dropForeign('pcn_email_jobs_motorbike_id_foreign');
            $table->dropForeign('pcn_email_jobs_user_id_foreign');
        });
    }
};
