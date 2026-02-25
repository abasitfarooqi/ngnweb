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
        Schema::table('pcn_cases', function (Blueprint $table) {
            $table->date('date_of_letter_issued')->nullable()->after('date_of_contravention');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pcn_cases', function (Blueprint $table) {
            $table->dropColumn('date_of_letter_issued');
        });
    }
};
