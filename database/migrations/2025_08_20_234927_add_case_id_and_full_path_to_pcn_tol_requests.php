<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pcn_tol_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('pcn_case_id')->nullable()->after('id');
            $table->string('full_path')->nullable()->after('status');

            // Foreign key to PCN Cases table
            $table->foreign('pcn_case_id')
                ->references('id')
                ->on('pcn_cases');
        });
    }

    public function down(): void
    {
        Schema::table('pcn_tol_requests', function (Blueprint $table) {
            $table->dropForeign(['pcn_case_id']);
            $table->dropColumn('pcn_case_id');
            $table->dropColumn('full_path');
        });
    }
};
