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
        if (Schema::hasColumn('pcn_cases', 'council_link')) {
            return;
        }
        Schema::table('pcn_cases', function (Blueprint $table) {
            $table->string('council_link')->nullable()->after('note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('pcn_cases', 'council_link')) {
            return;
        }
        Schema::table('pcn_cases', function (Blueprint $table) {
            $table->dropColumn('council_link');
        });
    }
};
