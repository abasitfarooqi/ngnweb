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
        Schema::table('contract_extra_items', function (Blueprint $table) {
            $table->foreign(['application_id'])->references(['id'])->on('finance_applications')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_extra_items', function (Blueprint $table) {
            $table->dropForeign('contract_extra_items_application_id_foreign');
        });
    }
};
