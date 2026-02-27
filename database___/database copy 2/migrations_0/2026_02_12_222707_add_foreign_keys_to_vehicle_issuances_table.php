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
        Schema::table('vehicle_issuances', function (Blueprint $table) {
            $table->foreign(['branch_id'])->references(['id'])->on('branches')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['customer_id'])->references(['id'])->on('customers')->onUpdate('cascade')->onDelete('set null');
            $table->foreign(['motorbike_id'])->references(['id'])->on('motorbikes')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_issuances', function (Blueprint $table) {
            $table->dropForeign('vehicle_issuances_branch_id_foreign');
            $table->dropForeign('vehicle_issuances_customer_id_foreign');
            $table->dropForeign('vehicle_issuances_motorbike_id_foreign');
            $table->dropForeign('vehicle_issuances_user_id_foreign');
        });
    }
};
