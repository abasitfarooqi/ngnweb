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
        Schema::table('pcn_case_updates', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['case_id']);

            // Add the foreign key constraint with the new onDelete action
            $table->foreign('case_id')->references('id')->on('pcn_cases')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcn_case_updates', function (Blueprint $table) {
            // Drop the foreign key constraint with the restrict action
            $table->dropForeign(['case_id']);

            // Add the original foreign key constraint with the cascade action
            $table->foreign('case_id')->references('id')->on('pcn_cases')->onDelete('cascade');
        });
    }
};
