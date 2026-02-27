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
            $table->boolean('is_transferred')
                ->default(false)
                ->after('note'); // change last_column_name to the column after which you want this
        });
    }

    public function down(): void
    {
        Schema::table('pcn_case_updates', function (Blueprint $table) {
            $table->dropColumn('is_transferred');
        });
    }
};
