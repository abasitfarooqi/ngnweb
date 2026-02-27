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
        Schema::table('pcn_tol_requests', function (Blueprint $table) {
            $table->foreign(['pcn_case_id'])->references(['id'])->on('pcn_cases')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['update_id'])->references(['id'])->on('pcn_case_updates')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcn_tol_requests', function (Blueprint $table) {
            $table->dropForeign('pcn_tol_requests_pcn_case_id_foreign');
            $table->dropForeign('pcn_tol_requests_update_id_foreign');
            $table->dropForeign('pcn_tol_requests_user_id_foreign');
        });
    }
};
