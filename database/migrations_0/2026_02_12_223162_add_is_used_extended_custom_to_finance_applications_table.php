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
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->boolean('is_used_extended_custom')->default(false)->after('is_used_extended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finance_applications', function (Blueprint $table) {
            $table->dropColumn('is_used_extended_custom');
        });
    }
};
