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
        Schema::table('purchase_agreement_accesses', function (Blueprint $table) {
            $table->foreign(['purchase_id'])->references(['id'])->on('purchase_used_vehicles')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_agreement_accesses', function (Blueprint $table) {
            $table->dropForeign('purchase_agreement_accesses_purchase_id_foreign');
        });
    }
};
